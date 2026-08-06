<?php

namespace App\Http\Controllers\Api;

use Validator;
use Response;
use File;
use Illuminate\Support\Facades\Log;
use DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Cache;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use App\INC\common_function;
use App\INC\user_functions;

class WebhooksController extends Controller
{

    /**
     * GDPR - Customer data request webhook
     */
    public function CustomerRequest(Request $request)
    {
        $cf_obj = new common_function();

        $shop_info = file_get_contents('php://input');
        $shop_info = json_decode($shop_info, TRUE);

        $selected_field = 'store_user_id,email';
        $where = ['shop' => $shop_info['shop_domain']];
        $table_shop_info_rs = $cf_obj->select_row(
            config('app.table_user_stores'), $selected_field, $where
        );

        $table_shop_info = (count($table_shop_info_rs) > 0 && !empty($table_shop_info_rs[0]->email))
            ? (array) $table_shop_info_rs[0]
            : ((!$table_shop_info_rs->isEmpty()) ? (array) $table_shop_info_rs[0] : []);

        if (!empty($table_shop_info) && $table_shop_info['email'] != '') {
            $fields = [
                'domain' => '', 'owner' => '', 'shop_plan' => '',
                'money_format' => '', 'currency' => '', 'address1' => '',
                'address2' => '', 'city' => '', 'country_name' => '',
                'phone' => '', 'province' => '', 'zip' => '',
                'timezone' => '', 'iana_timezone' => '', 'weight_unit' => ''
            ];

            $where = ['shop' => $shop_info['shop_domain']];
            $cf_obj->update(config('app.table_user_stores'), $fields, $where);

            $table_array = [];
            foreach ($table_array as $table) {
                $where = ['store_user_id' => $table_shop_info['store_user_id']];
                $cf_obj->delete($table, $where);
            }
        }

        return response()->json(['status' => 200, 'data' => ""]);
    }

    /**
     * GDPR - Shop info remove webhook
     */
    public function ShopinfoRemove(Request $request)
    {
        $shop = $this->resolve_shop();

        $cf_obj = new common_function();
        $us_obj = new User_functions($shop);

        $where = ['shop' => $shop];
        $shop_detail_rs = $cf_obj->select_row(
            config('app.table_user_stores'),
            'store_user_id, name, shop, email, token',
            $where
        );

        $shop_detail = (count($shop_detail_rs) > 0 && !empty($shop_detail_rs[0]->email))
            ? (array) $shop_detail_rs[0]
            : ((!$shop_detail_rs->isEmpty()) ? (array) $shop_detail_rs[0] : []);

        if (!empty($shop_detail)) {
            $shopdom = $shop_detail['shop'];
            $email   = $shop_detail['email'];
            $token   = $shop_detail['token'] ?? '';

            // Mark plugin as inactive
            $cf_obj->plugin_active_inactive($shop_detail, 0);

            // Notify Seers CMP backend
            $this->notify_seers_backend($shopdom, $email);

            // Remove old Script Tags if any
            if (!empty($token)) {
                $this->remove_script_tags($cf_obj, $shop, $token);
            }
        }

        // Update store status fields (soft deactivation — keep record)
        $fields = [
            'status'        => '0',
            'token'         => '',
            'app_status'    => '0',
            'toggle_status' => '0'
        ];
        $cf_obj->update(config('app.table_user_stores'), $fields, $where);

        return response()->json(['status' => 200, 'data' => ""]);
    }

    /**
     * App uninstalled webhook
     * After uninstall + reinstall → new install goes through App Embed flow
     */

    public function AppUninstalled(Request $request)
    {
        Log::info('AppUninstalled webhook received');

        $shop = $this->resolve_shop();

        Log::info('AppUninstalled - shop resolved: ' . $shop);

        if (empty($shop)) {
            Log::error('AppUninstalled - shop is empty, cannot process');
            return response()->json(['status' => 200, 'data' => '']);
        }

        $cf_obj = new common_function();

        $where = ['shop' => $shop];
        $shop_detail_rs = $cf_obj->select_row(
            config('app.table_user_stores'),
            'store_user_id, name, shop, email, token',
            $where
        );

        $shop_detail = (count($shop_detail_rs) > 0 && !empty($shop_detail_rs[0]->email))
            ? (array) $shop_detail_rs[0]
            : ((!$shop_detail_rs->isEmpty()) ? (array) $shop_detail_rs[0] : []);

        Log::info('AppUninstalled - shop_detail found: ' . (!empty($shop_detail) ? 'YES' : 'NO'));

        if (!empty($shop_detail)) {
            $shopdom = $shop_detail['shop'];
            $email   = $shop_detail['email'];
            $token   = $shop_detail['token'] ?? '';

            Log::info('AppUninstalled - notifying Seers backend');
            $cf_obj->plugin_active_inactive($shop_detail, 0);
            $this->notify_seers_backend($shopdom, $email);

            if (!empty($token)) {
                Log::info('AppUninstalled - removing script tags');
                $this->remove_script_tags($cf_obj, $shop, $token);
            }
        }

        Log::info('AppUninstalled - deleting DB record for shop: ' . $shop);
        $delete_result = $cf_obj->delete(config('app.table_user_stores'), $where);
        Log::info('AppUninstalled - delete result: ' . json_encode($delete_result));

        Log::info('=== AppUninstalled webhook completed ===');

        return response()->json(['status' => 200, 'data' => '']);
    }

    /**
     * Shop update webhook — updates store details in DB
     */
    public function ShopUpdate(Request $request)
    {
        $shop = $this->resolve_shop();

        $cf_obj = new common_function($shop);

        $hmac_header = $_SERVER['HTTP_X_SHOPIFY_HMAC_SHA256'];
        $shop_update = file_get_contents('php://input');

        $verified = $cf_obj->verify_webhook($shop_update, $hmac_header);

        if (!empty($cf_obj) && $verified && $cf_obj->is_json($shop_update)) {
            $shop_detail_arr = json_decode($shop_update, TRUE);

            $fields = [
                'currency'     => $shop_detail_arr['currency'],
                'money_format' => $cf_obj->mres($shop_detail_arr['money_format']),
                'owner'        => $shop_detail_arr['shop_owner'],
                'shop_plan'    => $shop_detail_arr['plan_name'],
                'address1'     => $shop_detail_arr['address1'],
                'address2'     => $shop_detail_arr['address2'],
                'city'         => $shop_detail_arr['city'],
                'country_name' => $shop_detail_arr['country_name'],
                'phone'        => $shop_detail_arr['phone'],
                'province'     => $shop_detail_arr['province'],
                'zip'          => $shop_detail_arr['zip'],
                'timezone'     => $shop_detail_arr['timezone'],
                'iana_timezone'=> $shop_detail_arr['iana_timezone'],
                'domain'       => $shop_detail_arr['domain'],
                'weight_unit'  => $shop_detail_arr['weight_unit'],
            ];

            $where = ['shop' => $shop];
            $cf_obj->update(config('app.table_user_stores'), $fields, $where);
        }

        return response()->json(['status' => 200, 'data' => ""]);
    }

    /**
     * Handles all frontend AJAX calls dynamically
     */
    public function AjaxAction(Request $request)
    {
        $is_bad_shop = 0;

        if (isset($_POST['shop']) && $_POST['shop'] != '') {
            $uf_obj = new User_functions($_POST['shop']);
            $current_user = $uf_obj->get_store_detail_obj();

            if (!empty($current_user)) {
                if (isset($_POST['method_name']) && $_POST['method_name'] != '') {
                    $response = call_user_func(array($uf_obj, $_POST['method_name']));
                    return json_encode($response);
                }
            } else {
                $is_bad_shop++;
            }
        } else {
            $is_bad_shop++;
        }

        if ($is_bad_shop > 0) {
            return json_encode([
                'result' => 'fail',
                'msg'    => 'Opps! Bad request call!',
                'code'   => '403'
            ]);
        }
    }

    // PRIVATE HELPER METHODS

    /**
     * Resolve shop domain from server headers or GET param.
     * Centralised to avoid repeating this logic in every webhook method.
     */
    private function resolve_shop(): string
    {
        // Always check HTTP header first — works for both live and local/ngrok
        if (!empty($_SERVER['HTTP_X_SHOPIFY_SHOP_DOMAIN'])) {
            return $_SERVER['HTTP_X_SHOPIFY_SHOP_DOMAIN'];
        }

        // Fallback for live server header format
        if (!empty($_SERVER['X-Shopify-Shop-Domain'])) {
            return $_SERVER['X-Shopify-Shop-Domain'];
        }

        // Fallback for GET param (OAuth flow)
        if (!empty($_GET['shop'])) {
            return $_GET['shop'];
        }

        return '';
    }

    /**
     * Notify Seers CMP backend to deactivate the store.
     * Used by both ShopinfoRemove and AppUninstalled.
     */
    private function notify_seers_backend(string $shopdom, string $email): void
    {
        $data = [
            'domain'      => $shopdom,
            'user_domain' => $shopdom,
            'email'       => $email,
            'user_email'  => $email,
            'secret'      => config('app.seers_api_secret'),
            'platform'    => 'shopify',
            'status'      => '0'
        ];

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL            => config('app.seers_api_base_url') . "banner-settings",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => "",
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => "POST",
            CURLOPT_POSTFIELDS     => $data
        ]);
        curl_exec($curl);
        curl_close($curl);
    }

    /**
     * Remove all Seers Script Tags from a store.
     * Cleans up old Script Tag installations on uninstall.
     * New installs will use Theme App Extension (App Embed) instead.
     */
    private function remove_script_tags($cf_obj, string $shop, string $token): void
    {
        try {
            $allscripts = $cf_obj->prepare_api_condition(
                ['script_tags'], [], 'GET', '0', $token, $shop
            );

            if (empty($allscripts['body']['script_tags'])) {
                return;
            }

            foreach ($allscripts['body']['script_tags'] as $script) {
                if (stripos($script['src'], 'cb.js') !== false ||
                    stripos($script['src'], 'seersco') !== false) {

                    $cf_obj->prepare_api_condition(
                        ['script_tags', $script['id']],
                        [], 'DELETE', '0', $token, $shop
                    );
                }
            }
        } catch (\Exception $e) {
            Log::error('remove_script_tags failed for ' . $shop . ': ' . $e->getMessage());
        }
    }
}
