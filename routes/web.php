<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\INC\common_function;
use Illuminate\Support\Facades\Log;

Route::get('/user', [UserController::class, 'userdahsboard']);

Route::get('/', function () {

    $__webhook_arr = array(
        'app/uninstalled',
        'shop/update'
    );

    $cf_obj  = new common_function();
    $thehost = !empty($_REQUEST['host']) ? $_REQUEST['host'] : '';

    if (isset($_REQUEST['shop']) && $_REQUEST['shop'] != "") {
        $shop           = isset($_POST['shop']) ? $_POST['shop'] : $_GET['shop'];
        $selected_field = 'store_user_id, token';
        $where          = array('shop' => $shop, 'status' => '1');
        $store_row      = $cf_obj->select_row(config('app.table_user_stores'), $selected_field, $where);

        if (isset($_GET['code'])) {
            // Prevent duplicate OAuth processing — same code used twice
            $cache_key = 'oauth_code_' . md5($_GET['code']);
            if (cache()->has($cache_key)) {
                Log::warning('OAuth - duplicate code detected, redirecting', [
                    'shop' => $shop,
                ]);
                // Just redirect to dashboard — first request already processed
                $cf_obj->shopify_redirect(
                    config('app.url_user') . '?shop=' . $shop . '&host=' . $thehost, 'REMOTE'
                );
                exit;
            }
            // Mark code as used for 5 minutes
            cache()->put($cache_key, true, now()->addMinutes(5));

            Log::info('OAuth callback hit', [
                'shop'         => $shop,
                'code_preview' => substr($_GET['code'], 0, 20),
            ]);

            // Direct curl for OAuth token exchange
            // prepare_api_condition appends .json which breaks the OAuth endpoint
            $oauth_url = "https://{$shop}/admin/oauth/access_token";

            $ch = curl_init($oauth_url);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CUSTOMREQUEST  => 'POST',
                CURLOPT_POSTFIELDS     => http_build_query([
                    'client_id'     => config('app.shopify_apikey'),
                    'client_secret' => config('app.shopify_secret'),
                    'code'          => $_GET['code'],
                ]),
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_HTTPHEADER     => [
                    'Content-Type: application/x-www-form-urlencoded',
                    'Accept: application/json',
                ],
            ]);

            $oauth_response = curl_exec($ch);
            $oauth_err      = curl_error($ch);
            curl_close($ch);

            Log::info('OAuth token exchange response', ['raw' => $oauth_response]);

            if ($oauth_err) {
                Log::error('OAuth curl error', ['error' => $oauth_err]);
                die('OAuth request failed: ' . $oauth_err);
            }

            $oauth_body = json_decode($oauth_response, true);

            if (empty($oauth_body['access_token'])) {
                Log::error('OAuth - no access token', ['response' => $oauth_body]);
                die('Failed to get access token: ' . json_encode($oauth_body));
            }

            $token = $oauth_body['access_token'];

            Log::info('OAuth token received', [
                'shop'          => $shop,
                'token_preview' => substr($token, 0, 30),
                'token_length'  => strlen($token),
            ]);

            // Fetch shop info
            $shop_responce = $cf_obj->prepare_api_condition(
                array('shop'), array(), 'GET', 0, $token, $shop
            );

            if (empty($shop_responce['body']['shop'])) {
                Log::error('Failed to get shop info', [
                    'shop'     => $shop,
                    'response' => $shop_responce
                ]);
                die('Failed to retrieve shop information');
            }

            $shopinfo = $shop_responce['body']['shop'];
            $email    = $shopinfo['email'];
            $domain   = $shopinfo['domain'];

            $shop_details = array(
                'email'         => $email,
                'name'          => $cf_obj->mres($shopinfo['name']),
                'shop'          => $shop,
                'host'          => $thehost,
                'domain'        => $shopinfo['domain'],
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
                'toggle_status' => isset($shopinfo['toggle_status']) && $shopinfo['toggle_status'] > -1
                                    ? $shopinfo['toggle_status'] : 0,
                'status'        => '1',
                'updated_on'    => date('Y-m-d H:i:s'),
            );

            // Check if record exists
            $is_store_exist = $cf_obj->select_row(
                config('app.table_user_stores'), '*', array('shop' => $shop)
            );

            if (!$is_store_exist->isEmpty() && !empty($is_store_exist[0]->store_user_id)) {

                // EXISTING CLIENT — update token + store details only
                $cf_obj->update(
                    config('app.table_user_stores'),
                    $shop_details,
                    array('shop' => $shop)
                );

                /* Register Webhooks */
                if (!empty($__webhook_arr)) {
                    foreach ($__webhook_arr as $topic) {
                        $file_name     = str_replace('/', '-', $topic);
                        $url_param_arr = array('webhook' => array(
                            'topic'   => $topic,
                            // 'address' => config('app.url') . 'api/' . $file_name,
                            'address' => rtrim(config('app.url'), '/') . '/api/' . $file_name,
                            'format'  => 'json'
                        ));
                        $cf_obj->prepare_api_condition(
                            array('webhooks'), $url_param_arr, 'POST', 0, $token, $shop
                        );
                    }
                }

                Log::info('OAuth - existing store token updated', [
                    'shop'          => $shop,
                    'token_preview' => substr($token, 0, 30),
                ]);

                $cf_obj->shopify_redirect(
                    config('app.url_user') . '?shop=' . $shop . '&host=' . $thehost, 'REMOTE'
                );
                exit;

            } else {

                // NEW CLIENT - insert and set up Theme App Embedding
                $shop_details['created_on'] = date('Y-m-d H:i:s');
                $store_user_id = $cf_obj->insert(
                    config('app.table_user_stores'), $shop_details
                );

                /* Register Webhooks */
                if (!empty($__webhook_arr)) {
                    foreach ($__webhook_arr as $topic) {
                        $file_name     = str_replace('/', '-', $topic);
                        $url_param_arr = array('webhook' => array(
                            'topic'   => $topic,
                            // 'address' => config('app.url') . 'api/' . $file_name,
                            'address' => rtrim(config('app.url'), '/') . '/api/' . $file_name,
                            'format'  => 'json'
                        ));
                        $cf_obj->prepare_api_condition(
                            array('webhooks'), $url_param_arr, 'POST', 0, $token, $shop
                        );
                    }
                }

                $cf_obj->plugin_active_inactive($shop_details, 1);
                $cf_obj->activate_app_embed($shop, $token, $domain, $email);
                $cf_obj->insertConsentTrackingScript($shop, $token);

                Log::info('OAuth - new store inserted', [
                    'shop'          => $shop,
                    'store_user_id' => $store_user_id,
                    'token_preview' => substr($token, 0, 30),
                ]);

                $cf_obj->shopify_redirect(
                    config('app.url_user') . '?shop=' . $shop . '&host=' . $thehost, 'REMOTE'
                );
                exit;
            }

        } else {
            if (!$store_row->isEmpty()) {
                $cf_obj->shopify_redirect(
                    config('app.url_user') . '?shop=' . $shop . '&host=' . $thehost, 'APP'
                );
                exit;
            } else {
                $install_url = "https://" . $shop . "/admin/oauth/authorize?client_id="
                    . config('app.shopify_apikey')
                    . "&scope=" . urlencode(config('app.shopify_scope'))
                    . "&redirect_uri=" . urlencode(config('app.url'));
                $cf_obj->shopify_redirect($install_url, 'REMOTE');
                exit;
            }
        }
    } else {
        echo 'Directory access is forbidden.';
        exit;
    }
});
