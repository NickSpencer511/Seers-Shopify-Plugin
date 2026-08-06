<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\INC\common_function;
use Illuminate\Support\Facades\Log;

Route::get('/user', [UserController::class, 'userdahsboard']);

Route::get('/', function () {

    $__webhook_arr = ['app/uninstalled', 'shop/update'];
    $cf_obj  = new common_function();
    $thehost = request('host', '');

    if (!request()->filled('shop')) {
        echo 'Directory access is forbidden.';
        exit;
    }

    $shop      = request('shop');
    $where     = ['shop' => $shop, 'status' => '1'];
    $store_row = $cf_obj->select_row(config('app.table_user_stores'), 'store_user_id, token', $where);

    if (request()->filled('code')) {

        $code = request('code');

        Log::info('OAuth entered', ['shop' => $shop, 'query' => request()->query()]);

        // Exchange code for token
        $ch = curl_init("https://{$shop}/admin/oauth/access_token");
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST  => 'POST',
            CURLOPT_POSTFIELDS     => http_build_query([
                'client_id'     => config('app.shopify_apikey'),
                'client_secret' => config('app.shopify_secret'),
                'code'          => $code,
            ]),
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_HTTPHEADER     => ['Content-Type: application/x-www-form-urlencoded'],
        ]);
        $oauth_response = curl_exec($ch);
        $oauth_err      = curl_error($ch);
        curl_close($ch);

        if ($oauth_err) {
            Log::error('OAuth curl error', ['shop' => $shop, 'error' => $oauth_err]);
            die('OAuth request failed: ' . $oauth_err);
        }

        $oauth_body = json_decode($oauth_response, true);

        if (empty($oauth_body['access_token'])) {
            Log::error('OAuth - no access token', ['shop' => $shop, 'response' => $oauth_body]);
            die('Failed to get access token: ' . json_encode($oauth_body));
        }

        $token = $oauth_body['access_token'];
        Log::info('OAuth token received', ['shop' => $shop, 'token_preview' => substr($token, 0, 10)]);

        // Get shop info
        $shop_resp = $cf_obj->prepare_api_condition(['shop'], [], 'GET', 0, $token, $shop);

        if (empty($shop_resp['body']['shop'])) {
            Log::error('Failed to get shop info', ['shop' => $shop]);
            die('Failed to retrieve shop information');
        }

        $shopinfo    = $shop_resp['body']['shop'];
        $email       = $shopinfo['email'];
        $domain      = $shopinfo['domain'];
        $shop_details = [
            'email'         => $email,
            'name'          => $cf_obj->mres($shopinfo['name']),
            'shop'          => $shop,
            'host'          => $thehost,
            'domain'        => $domain,
            'token'         => $token,
            'owner'         => $shopinfo['shop_owner'],
            'shop_plan'     => $shopinfo['plan_name'],
            'money_format'  => $cf_obj->mres(strip_tags($shopinfo['money_format'])),
            'currency'      => $shopinfo['currency'],
            'address1'      => $shopinfo['address1'],
            'address2'      => $shopinfo['address2'],
            'city'          => $shopinfo['city'],
            'country_name'  => $shopinfo['country_name'],
            'phone'         => $shopinfo['phone'],
            'province'      => $shopinfo['province'],
            'zip'           => $shopinfo['zip'],
            'timezone'      => $shopinfo['timezone'],
            'iana_timezone' => $shopinfo['iana_timezone'],
            'weight_unit'   => $shopinfo['weight_unit'],
            'toggle_status' => 0,
            'status'        => '1',
            'updated_on'    => date('Y-m-d H:i:s'),
        ];

        $is_store_exist = $cf_obj->select_row(config('app.table_user_stores'), '*', ['shop' => $shop]);

        if (!$is_store_exist->isEmpty() && !empty($is_store_exist[0]->store_user_id)) {
            $cf_obj->update(config('app.table_user_stores'), $shop_details, ['shop' => $shop]);
            Log::info('OAuth - existing store updated', ['shop' => $shop]);
        } else {
            $shop_details['created_on'] = date('Y-m-d H:i:s');
            $store_user_id = $cf_obj->insert(config('app.table_user_stores'), $shop_details);
            $cf_obj->plugin_active_inactive($shop_details, 1);
            Log::info('OAuth - new store inserted', ['shop' => $shop, 'id' => $store_user_id]);
        }

        // Register webhooks
        foreach ($__webhook_arr as $topic) {
            $file_name = str_replace('/', '-', $topic);
            $cf_obj->prepare_api_condition(['webhooks'], [
                'webhook' => [
                    'topic'   => $topic,
                    'address' => rtrim(config('app.url'), '/') . '/api/' . $file_name,
                    'format'  => 'json',
                ]
            ], 'POST', 0, $token, $shop);
        }

        // Activate app embed and get editor URL
        $embed = $cf_obj->activate_app_embed($shop, $token, $domain, $email);
        Log::info('OAuth - embed result', ['shop' => $shop, 'embed' => $embed]);

        if (!empty($embed['editor_url'])) {
            header('Location: ' . $embed['editor_url']);
            exit;
        }

        header('Location: ' . config('app.url_user') . '?shop=' . $shop . '&host=' . $thehost);
        exit;

    } else {

        if (!$store_row->isEmpty()) {
            $cf_obj->shopify_redirect(
                config('app.url_user') . '?shop=' . $shop . '&host=' . $thehost,
                'APP'
            );
            exit;
        }

        $install_url = "https://{$shop}/admin/oauth/authorize?client_id="
            . config('app.shopify_apikey')
            . "&scope=" . urlencode(config('app.shopify_scope'))
            . "&redirect_uri=" . urlencode(rtrim(config('app.url'), '/'));

        Log::info('Redirecting to Shopify install', ['shop' => $shop, 'install_url' => $install_url]);

        header('Location: ' . $install_url);
        exit;
    }
});
