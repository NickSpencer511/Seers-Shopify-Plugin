<?php

namespace Tests\Feature;

use App\INC\common_function;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

/**
 * Runs on an in-memory sqlite DB, outbound HTTP goes to a closed local port,
 * so no real store, Shopify or Seers API is touched.
 */
class SecurityHardeningTest extends TestCase
{
    private const SECRET = 'test-shopify-secret';
    private const SHOP   = 'test-store.myshopify.com';

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'database.default'                    => 'sqlite',
            'database.connections.sqlite.database' => ':memory:',
            'app.table_user_stores'               => 'user_stores',
            'app.shopify_secret'                  => self::SECRET,
            'app.shopify_webhook_hmac_enforce'    => true,
            'app.seers_api_base_url'              => 'http://127.0.0.1:9/',
            'app.cmp_vapor_api_url'               => 'http://127.0.0.1:9/',
            'logging.default'                     => 'null',
            'session.driver'                      => 'array',
        ]);
        DB::purge('sqlite');
        // local APP_URL has a sub-path (/shopify-plugin-hooks/public/) which breaks test routing
        URL::forceRootUrl('http://localhost');

        Schema::create('user_stores', function (Blueprint $t) {
            $t->increments('store_user_id');
            foreach ([
                'shop', 'name', 'email', 'token', 'status', 'app_status', 'app_plan', 'toggle_status',
                'charge_approve', 'domain', 'host', 'owner', 'shop_plan', 'money_format', 'currency',
                'address1', 'address2', 'city', 'country_name', 'phone', 'province', 'zip', 'timezone',
                'iana_timezone', 'weight_unit', 'data_key', 'domain_id', 'user_id', 'cb_js_url',
                'access_token', 'user_domain', 'user_email', 'created_on', 'updated_on',
            ] as $col) {
                $t->string($col)->nullable();
            }
        });

        DB::table('user_stores')->insert([
            'shop'     => self::SHOP,
            'name'     => 'Test Store',
            'email'    => 'owner@example.com',
            'token'    => '',   // empty so no Shopify API calls are attempted
            'status'   => '1',
            'domain'   => 'test-store.com',
            'data_key' => 'abc',
        ]);

        $_POST = [];
        unset($_SERVER['HTTP_X_SHOPIFY_SHOP_DOMAIN']);
    }

    private function hmac(string $body): string
    {
        return base64_encode(hash_hmac('sha256', $body, self::SECRET, true));
    }

    private function webhook(string $uri, string $body, ?string $hmac)
    {
        $_SERVER['HTTP_X_SHOPIFY_SHOP_DOMAIN'] = self::SHOP;
        $headers = ['CONTENT_TYPE' => 'application/json', 'HTTP_X_SHOPIFY_SHOP_DOMAIN' => self::SHOP];
        if ($hmac !== null) {
            $headers['HTTP_X_SHOPIFY_HMAC_SHA256'] = $hmac;
        }
        return $this->call('POST', $uri, [], [], [], $headers, $body);
    }

    private function ajax(array $post)
    {
        $_POST = $post;
        return $this->post('/api/ajax_actions', $post);
    }

    private function storeExists(): bool
    {
        return DB::table('user_stores')->where('shop', self::SHOP)->exists();
    }

    // ---------- Webhooks ----------

    public function test_uninstall_webhook_without_hmac_is_rejected_and_store_kept()
    {
        $this->webhook('/api/app-uninstalled', '{"id":1}', null)->assertStatus(401);
        $this->assertTrue($this->storeExists());
    }

    public function test_uninstall_webhook_with_wrong_hmac_is_rejected_and_store_kept()
    {
        $this->webhook('/api/app-uninstalled', '{"id":1}', $this->hmac('other body'))->assertStatus(401);
        $this->assertTrue($this->storeExists());
    }

    public function test_uninstall_webhook_signed_by_shopify_still_works()
    {
        $body = '{"id":1,"domain":"test-store.com"}';
        $this->webhook('/api/app-uninstalled', $body, $this->hmac($body))->assertStatus(200);
        $this->assertFalse($this->storeExists());
    }

    public function test_shop_redact_webhook_requires_hmac()
    {
        $body = '{"shop_domain":"' . self::SHOP . '"}';
        $this->webhook('/api/shop-info-remove', $body, null)->assertStatus(401);
        $this->assertSame('', DB::table('user_stores')->value('token') ?? '');
        $this->assertSame('1', DB::table('user_stores')->value('status'));

        $this->webhook('/api/shop-info-remove', $body, $this->hmac($body))->assertStatus(200);
        $this->assertSame('0', DB::table('user_stores')->value('status'));
    }

    public function test_customer_request_webhook_requires_hmac()
    {
        $body = '{"shop_domain":"' . self::SHOP . '"}';
        $this->webhook('/api/customer-request', $body, 'bad')->assertStatus(401);
        $this->assertSame('test-store.com', DB::table('user_stores')->value('domain'));

        $this->webhook('/api/customer-request', $body, $this->hmac($body))->assertStatus(200);
    }

    public function test_customer_data_webhook_exists_and_requires_hmac()
    {
        $body = '{"shop_domain":"' . self::SHOP . '"}';
        $this->webhook('/api/customer-data', $body, null)->assertStatus(401);
        $this->webhook('/api/customer-data', $body, $this->hmac($body))->assertStatus(200);
    }

    public function test_shop_update_webhook_signed_by_shopify_still_updates()
    {
        $body = json_encode([
            'currency' => 'GBP', 'money_format' => '£{{amount}}', 'shop_owner' => 'Owner', 'plan_name' => 'basic',
            'address1' => 'a', 'address2' => '', 'city' => 'London', 'country_name' => 'UK', 'phone' => '',
            'province' => '', 'zip' => 'N1', 'timezone' => 'GMT', 'iana_timezone' => 'Europe/London',
            'domain' => 'test-store.com', 'weight_unit' => 'kg',
        ]);
        $this->webhook('/api/shop-update', $body, null)->assertStatus(401);
        $this->assertNull(DB::table('user_stores')->value('city'));

        $this->webhook('/api/shop-update', $body, $this->hmac($body))->assertStatus(200);
        $this->assertSame('London', DB::table('user_stores')->value('city'));
    }

    public function test_log_only_mode_lets_unsigned_webhooks_through()
    {
        config(['app.shopify_webhook_hmac_enforce' => false]);
        $this->webhook('/api/customer-data', '{}', null)->assertStatus(200);
    }

    // ---------- ajax_actions ----------

    public function test_ajax_cannot_read_store_row_with_token()
    {
        DB::table('user_stores')->update(['token' => 'shpat_secret_value']);

        $res = $this->ajax(['shop' => self::SHOP, 'method_name' => 'get_store_detail_obj']);

        $this->assertStringNotContainsString('shpat_secret_value', $res->getContent());
        $this->assertStringContainsString('"result":"fail"', $res->getContent());
    }

    public function test_ajax_blocks_other_internal_methods()
    {
        foreach (['get_store_email', 'set_user_data', 'activate_app_embed', 'plugin_active_inactive', 'last_query'] as $m) {
            $res = $this->ajax(['shop' => self::SHOP, 'method_name' => $m]);
            $this->assertStringContainsString('"result":"fail"', $res->getContent(), $m);
        }
    }

    public function test_ajax_allowed_method_still_runs()
    {
        // returns early with a validation message, no external call
        $res = $this->ajax(['shop' => self::SHOP, 'method_name' => 'lookup_domain_by_key']);
        $this->assertStringContainsString('Domain Group ID and shop are required.', $res->getContent());
    }

    public function test_every_method_the_dashboard_uses_is_allowed()
    {
        $used = [];
        foreach (['public/js/custom.js', 'resources/views/user.blade.php'] as $file) {
            $src = file_get_contents(base_path($file));
            // skip commented-out lines
            $src = preg_replace('#^\s*//.*$#m', '', $src);
            preg_match_all('/method_name\s*:\s*[\'"]([A-Za-z_]+)[\'"]/', $src, $m);
            $used = array_merge($used, $m[1]);
        }
        $allowed = (new \ReflectionClassConstant(\App\Http\Controllers\Api\WebhooksController::class, 'AJAX_METHODS'))->getValue();

        $this->assertNotEmpty($used);
        foreach (array_unique($used) as $method) {
            $this->assertContains($method, $allowed, "Dashboard calls $method but it is not allowed");
        }
    }

    // ---------- OAuth / shop domain ----------

    public function test_install_rejects_non_shopify_hosts()
    {
        foreach (['attacker.com', 'evil.com/admin', 'x.myshopify.com.evil.com', 'a.myshopify.com@evil.com'] as $shop) {
            $this->get('/?shop=' . urlencode($shop) . '&code=abc&hmac=x')->assertStatus(400);
        }
    }

    public function test_oauth_callback_with_bad_hmac_is_rejected()
    {
        $this->get("/?code=abc&shop=" . self::SHOP . "&timestamp=1&hmac=deadbeef")->assertStatus(403);
        $this->get('/?code=abc&shop=' . self::SHOP . '&timestamp=1')->assertStatus(403);
    }

    public function test_shop_domain_validation()
    {
        $cf = new common_function();
        $this->assertTrue($cf->is_valid_shop_domain('test-store.myshopify.com'));
        $this->assertTrue($cf->is_valid_shop_domain('Store123.myshopify.com'));
        $this->assertFalse($cf->is_valid_shop_domain('attacker.com'));
        $this->assertFalse($cf->is_valid_shop_domain('-bad.myshopify.com'));
        $this->assertFalse($cf->is_valid_shop_domain("a.myshopify.com\n"));
        $this->assertFalse($cf->is_valid_shop_domain(['a.myshopify.com']));
    }

    public function test_oauth_hmac_accepts_genuine_shopify_signature()
    {
        $cf = new common_function();
        // Real Shopify callback shape, host is base64 with "=" padding
        $params = [
            'code'      => '0907a61c0c8d55e99db179b68161bc00',
            'host'      => base64_encode('admin.shopify.com/store/test-store'),
            'shop'      => self::SHOP,
            'state'     => '0.6784241404160823',
            'timestamp' => '1337178173',
        ];
        ksort($params);
        $message = http_build_query($params);
        $hmac    = hash_hmac('sha256', $message, self::SECRET);

        $this->assertTrue($cf->verify_oauth_hmac($message . '&hmac=' . $hmac));
        // hmac in the middle of the query, as Shopify often sends it
        $this->assertTrue($cf->verify_oauth_hmac('code=' . $params['code'] . '&hmac=' . $hmac . '&' . http_build_query(array_diff_key($params, ['code' => 1]))));
        // Laravel's normalized getQueryString() form (RFC3986, sorted) - used if QUERY_STRING is missing
        $normalized = \Illuminate\Http\Request::create('/?' . $message . '&hmac=' . $hmac)->getQueryString();
        $this->assertTrue($cf->verify_oauth_hmac($normalized));
        $this->assertFalse($cf->verify_oauth_hmac($message . '&hmac=' . str_repeat('0', 64)));
        $this->assertFalse($cf->verify_oauth_hmac(str_replace('test-store', 'other', $message) . '&hmac=' . $hmac));
        $this->assertFalse($cf->verify_oauth_hmac($message));
    }

    public function test_webhook_hmac_helper()
    {
        $cf = new common_function();
        $this->assertTrue($cf->verify_webhook('{"a":1}', $this->hmac('{"a":1}')));
        $this->assertFalse($cf->verify_webhook('{"a":1}', $this->hmac('{"a":2}')));
        $this->assertFalse($cf->verify_webhook('{"a":1}', null));
        $this->assertFalse($cf->verify_webhook('{"a":1}', ''));
    }
}
