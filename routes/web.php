<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\INC\common_function;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/*Route::get('/', function () {
    return view('welcome');
});*/

Route::get('/', function () {
    
    /* * ****************************************
    *            WEBHOOK ARRAY               *
    * **************************************** */
    /*
    * When we need to add webhook you need to add topic into 
    * array list and need to make(add) file with same as topic name 
    * just replace "/" (slash) with "-" (hypehn,minus) sign
    * for e.g app-unistalled.php
    */

    $__webhook_arr = array(
        'app/uninstalled',
        'shop/update'
    );

    /* * ****************************************
    *          WEBHOOK ARRAY END             *
    * **************************************** */

    /* create object common function */
    $cf_obj = new common_function();

    if (isset($_REQUEST['shop']) && $_REQUEST['shop'] != "") {
        $shop = isset($_POST['shop']) ? $_POST['shop'] : $_GET['shop'];
        $selected_field = 'store_user_id, token';
        $where = array('shop' => $shop, 'status' => '1');
        $store_row = $cf_obj->select_row(config('app.table_user_stores'), $selected_field, $where);

        if (isset($_GET['code'])) {
            $thehost = (!empty($_REQUEST['host']) ? $_REQUEST['host'] : "");
            $url_param_arr = array('client_id' => config('app.shopify_apikey'), 'client_secret' => config('app.shopify_secret'), 'code' => $_GET['code']);
            $responce = $cf_obj->prepare_api_condition(array('oauth', 'access_token'), $url_param_arr, 'POST', 0, '', $shop);
            
            $token = $responce['body']['access_token'];
            if (!$store_row->isEmpty()) {
                header('Location: ' . config('app.url_user') . '?shop=' . $shop);
            } else {
                $responce = $cf_obj->prepare_api_condition(array('shop'), array(), 'GET', 0, $token, $shop);
                
                $shopinfo = $responce['body']['shop'];
                /* Register Webhook */
                if (!empty($__webhook_arr)) {
                    foreach ($__webhook_arr as $topic) {
                        //$file_name = str_replace('/', '-', $topic) . '.php';
                        $file_name = str_replace('/', '-', $topic);
                        $url_param_arr = array('webhook' => array(
                                'topic' => $topic,
                                'address' => config('app.url') . 'api/' . $file_name,
                                'format' => 'json'
                        ));
                        $cf_obj->prepare_api_condition(array('webhooks'), $url_param_arr, 'POST', 0, $token, $shop);
                    }
                }

                $email = $shopinfo['email'];
                $domain = $shopinfo['domain'];
                $timezone = $shopinfo['iana_timezone'];
                $shop_name = $shopinfo['name'];
                $shop_details = array(
                    'email' => $email,
                    'name' => $cf_obj->mres($shopinfo['name']),
                    'shop' => $shop,
                    'host' => $thehost,
                    'domain' => $shopinfo['domain'],
                    'token' => $token,
                    'owner' => $shopinfo['shop_owner'],
                    'shop_plan' => $shopinfo['plan_name'],
                    'money_format' => $cf_obj->mres(strip_tags($shopinfo['money_format'])),
                    'currency' => $shopinfo['currency'],
                    'address1' => $shopinfo['address1'],
                    'address2' => $shopinfo['address2'],
                    'city' => $shopinfo['city'],
                    'country_name' => $shopinfo['country_name'],
                    'phone' => $shopinfo['phone'],
                    'province' => $shopinfo['province'],
                    'zip' => $shopinfo['zip'],
                    'timezone' => $shopinfo['timezone'],
                    'iana_timezone' => $shopinfo['iana_timezone'],
                    'weight_unit' => $shopinfo['weight_unit'],
                    'toggle_status' => isset($shopinfo['toggle_status']) && $shopinfo['toggle_status'] > -1 ? $shopinfo['toggle_status'] : 0
                );

                // Get data key and related info from Seers API
                $data_key_response = $cf_obj->get_data_key($domain, $email);
                $datakey = $data_key_response['key'] ?? '';
                $access_token = $data_key_response['access_token'] ?? '';
                $domain_id = $data_key_response['domain_id'] ?? '';
                $user_id = $data_key_response['user_id'] ?? '';
                $cdnbaseurl = $data_key_response['cdnbaseurl'] ?? 'https://cdn.seersco.com/';

                $selected_field = '*';
                $where = array('shop' => $shop);
                $is_store_exist = $cf_obj->select_row(config('app.table_user_stores'), $selected_field, $where);

                // Build fields for database (now includes Seers data and embed_enabled)
                $fields = array_merge($shop_details, [
                    'data_key' => $datakey,
                    'access_token' => $access_token,
                    'domain_id' => $domain_id,
                    'user_id' => $user_id,
                    'embed_enabled' => 0,          // new column, default 0
                    'status' => '1',
                    'updated_on' => date('Y-m-d H:i:s'),
                ]);

                if (!$is_store_exist->isEmpty() && !empty($is_store_exist[0]) && !empty($is_store_exist[0]->store_user_id)) {
                    $where = array('shop' => $shop);
                    $last_id = $cf_obj->update(config('app.table_user_stores'), $fields, $where);
                    $store_user_id = $is_store_exist[0]->store_user_id;
                    $was_existing = true;
                } else {
                    $fields['created_on'] = date('Y-m-d H:i:s');
                    $store_user_id = $cf_obj->insert(config('app.table_user_stores'), $fields);
                    $was_existing = false;
                }

                // Build script URL for metafield
                if (!empty($user_id) && !empty($domain_id)) {
                    $script_url = $cdnbaseurl . 'banners/' . $user_id . '/' . $domain_id . '/cb.js?param=' . $datakey . '&name=CookieXray&shop=' . $shop;
                } else {
                    $script_url = 'https://cdn.seersco.com/banners/default/default/cb.js?param=' . $datakey . '&name=CookieXray&shop=' . $shop;
                }

                // Save script URL as metafield (always do this)
                $cf_obj->set_shop_metafield($shop, $token, 'cmp_script_url', $script_url, 'seers', 'string');

                // Active this plugin (existing call)
                $cf_obj->plugin_active_inactive($fields, 1);

                // Conditionally insert script tags (only for existing stores)
                if ($was_existing) {
                    // Existing store: keep ScriptTags for backward compatibility
                    $cf_obj->snippest_insert($shop, $token, $domain, $email);
                    $cf_obj->insertConsentTrackingScript($shop, $token);
                } else {
                    // New store: no ScriptTags, rely on embed
                    $cf_obj->insertConsentTrackingScript($shop, $token); // keep if needed
                    // embed_enabled is already 0
                }

                header('Location: ' . config('app.url_user') . '?shop=' . $shop);
                exit;
            }
        } else {
            /* Check store is active or not */
            if (!$store_row->isEmpty()) {
                header('Location: ' . config('app.url_user') . '?shop=' . $shop);
                exit;
            } else {
                $install_url = "https://" . $shop . "/admin/oauth/authorize?client_id=" . config('app.shopify_apikey') . "&scope=" . urlencode(config('app.shopify_scope')) . "&redirect_uri=" . urlencode(config('app.url'));
                header("Location: " . $install_url);
                exit;
            }
        }
    } else {
        echo 'Directory access is forbidden.';
        exit;
    }

});

Route::get('/user', [UserController::class, 'userdahsboard']);
