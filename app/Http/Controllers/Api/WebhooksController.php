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

    public function CustomerRequest(Request $request)
    {

        /* Common function object */
        $cf_obj = new common_function();

        $shop_info = file_get_contents('php://input');

        /* shop info array */
        $shop_info = json_decode($shop_info, TRUE);

        $selected_field = 'store_user_id,email';
        $where = array('shop' => $shop_info['shop_domain']);
        $table_shop_info_rs = $cf_obj->select_row(config('app.table_user_stores'), $selected_field, $where);

        $table_shop_info = (count($table_shop_info_rs) > 0 && !empty($table_shop_info_rs[0]->email)) ? (array) $table_shop_info_rs[0] : ((!$table_shop_info_rs->isEmpty()) ? (array) $table_shop_info_rs[0] : [] ) ;

        if (!empty($table_shop_info) && $table_shop_info['email'] != '') {
            $fields = array(
                'domain' => '',
                'owner' => '',
                'shop_plan' => '',
                'money_format' => '',
                'currency' => '',
                'address1' => '',
                'address2' => '',
                'city' => '',
                'country_name' => '',
                'phone' => '',
                'province' => '',
                'zip' => '',
                'timezone' => '',
                'iana_timezone' => '',
                'weight_unit' => ''
            );

            $where = array('shop' => $shop_info['shop_domain']);
            $cf_obj->update(config('app.table_user_stores'), $fields, $where);

            /**
             * Declare array table data deleted after app uninstall rule GDPR
             */
            $table_array = array();

            foreach ($table_array as $table) {
                $where = array('store_user_id' => $table_shop_info['store_user_id']);
                $cf_obj->delete($table, $where);
            }
        }

        return response()->json([
            'status' => 200,
            'data'  => ""
        ]);

    }

    public function ShopinfoRemove (Request $request) {


        $siteurl = config("app.url");
        // if (stripos($siteurl, 'moonlit-sunset-rdslgfrkte0s.vapor-farm-e1') !== false) {
        if (stripos($siteurl, 'gdpr-cookie-consent-banner-cookie-notice-seers') !== false) {
            $shop = isset($_SERVER['X-Shopify-Shop-Domain']) ? $_SERVER['X-Shopify-Shop-Domain'] : $_SERVER['HTTP_X_SHOPIFY_SHOP_DOMAIN'];
        } else {
            $shop = (!empty($_GET['shop'])) ? $_GET['shop'] : "";
        }
        
        $cf_obj = new common_function();
        $us_obj = new User_functions($shop);
        
        $shop_name = $email = $store_user_id = '';
        $where = array('shop' => $shop);
        $shop_detail_rs = $cf_obj->select_row(config('app.table_user_stores'), 'store_user_id, name, shop, email', $where);

        $shop_detail = (count($shop_detail_rs) > 0 && !empty($shop_detail_rs[0]->email)) ? (array) $shop_detail_rs[0] : ((!$shop_detail_rs->isEmpty()) ? (array) $shop_detail_rs[0] : [] ) ;

        if(!empty($shop_detail)){
            $store_user_id = $shop_detail['store_user_id'];
            $shop_name = $shop_detail['name'];
            $shopdom = $shop_detail['shop'];
            $email = $shop_detail['email'];
            
            //save plugin is deactive on plugins db this plugin
            $cf_obj->plugin_active_inactive($shop_detail, 0);
            
            //remove the js script from html
            // SEND API CALL
                $data = array(
                    'domain' => $shopdom,
                    'user_domain' => $shopdom,
                    'email' => $email,
                    'user_email' => $email,
                    'secret' => '$2y$10$9ygTfodVBVM0XVCdyzEUK.0FIuLnJT0D42sIE6dIu9r/KY3XaXXyS',
                    'platform' => 'shopify',
                    'status'=>'0'
                );
        
        //         /******* Curl call start *****/
                $curl = curl_init();
        
                curl_setopt_array($curl, array(
                    CURLOPT_URL => "https://cmp.seersco.com/api/v2/banner-settings",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_SSL_VERIFYPEER => false,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "POST",
                    CURLOPT_POSTFIELDS => $data
                ));
        
                $response = curl_exec($curl);
                $error_number = curl_errno($curl);
                $error_message = curl_error($curl);
                curl_close($curl);
        
                $result =  json_decode($response, TRUE);
        }
        
        $fields = array(
            'status' => '0',
            'token' => '',
            'app_status' => '0',
            'toggle_status' => '0'
        );
        $where = array('shop' => $shop);
        $cf_obj->update(config('app.table_user_stores'), $fields, $where);

        return response()->json([
            'status' => 200,
            'data'  => ""
        ]);

    }


    // public function ShopinfoRemove (Request $request) {
    //     $shared_secret = env('SHOPIFY_APP_SECRET'); 
    
    //     $hmac_header = $request->header('X-Shopify-Hmac-SHA256');
    
    //     $data = file_get_contents('php://input');
    
    //     $calculated_hmac = base64_encode(hash_hmac('sha256', $data, $shared_secret, true));
    
    //     if (hash_equals($hmac_header, $calculated_hmac)) {
    
    //         $siteurl = config("app.url");
    
    //         if (stripos($siteurl, 'gdpr-cookie-consent-banner-cookie-notice-seers') !== false) {
    //             $shop = isset($_SERVER['X-Shopify-Shop-Domain']) ? $_SERVER['X-Shopify-Shop-Domain'] : $_SERVER['HTTP_X_SHOPIFY_SHOP_DOMAIN'];
    //         } else {
    //             $shop = (!empty($_GET['shop'])) ? $_GET['shop'] : "";
    //         }
            
    //         $cf_obj = new common_function();
    //         $us_obj = new User_functions($shop);
            
    //         $shop_name = $email = $store_user_id = '';
    //         $where = array('shop' => $shop);
    //         $shop_detail_rs = $cf_obj->select_row(config('app.table_user_stores'), 'store_user_id, name, shop, email', $where);
    
    //         $shop_detail = (count($shop_detail_rs) > 0 && !empty($shop_detail_rs[0]->email)) ? (array) $shop_detail_rs[0] : ((!$shop_detail_rs->isEmpty()) ? (array) $shop_detail_rs[0] : [] ) ;
    
    //         if (!empty($shop_detail)) {
    //             $store_user_id = $shop_detail['store_user_id'];
    //             $shop_name = $shop_detail['name'];
    //             $shopdom = $shop_detail['shop'];
    //             $email = $shop_detail['email'];
                
    //             $cf_obj->plugin_active_inactive($shop_detail, 0);
                
    //             $data = array(
    //                 'domain' => $shopdom,
    //                 'user_domain' => $shopdom,
    //                 'email' => $email,
    //                 'user_email' => $email,
    //                 'secret' => '$2y$10$9ygTfodVBVM0XVCdyzEUK.0FIuLnJT0D42sIE6dIu9r/KY3XaXXyS',
    //                 'platform' => 'shopify',
    //                 'status' => '0'
    //             );
            
    //             $curl = curl_init();
    //             curl_setopt_array($curl, array(
    //                 CURLOPT_URL => "https://seersco.com/api/banner-settings",
    //                 CURLOPT_RETURNTRANSFER => true,
    //                 CURLOPT_ENCODING => "",
    //                 CURLOPT_MAXREDIRS => 10,
    //                 CURLOPT_TIMEOUT => 0,
    //                 CURLOPT_FOLLOWLOCATION => true,
    //                 CURLOPT_SSL_VERIFYPEER => false,
    //                 CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    //                 CURLOPT_CUSTOMREQUEST => "POST",
    //                 CURLOPT_POSTFIELDS => $data
    //             ));
            
    //             $response = curl_exec($curl);
    //             curl_close($curl);
    
    //             $result = json_decode($response, TRUE);
    //         }
            
    //         $fields = array(
    //             'status' => '0',
    //             'token' => '',
    //             'app_status' => '0',
    //             'toggle_status' => '0'
    //         );
    //         $where = array('shop' => $shop);
    //         $cf_obj->update(config('app.table_user_stores'), $fields, $where);
    
    //         return response()->json([
    //             'status' => 200,
    //             'data' => ""
    //         ]);
    
    //     } else {
    //         return response()->json([
    //             'status' => 'error',
    //             'message' => 'Invalid HMAC signature.'
    //         ], 401);
    //     }
    // }
    

    public function AppUninstalled (Request $request) {


        $siteurl = config("app.url");
        // if (stripos($siteurl, 'moonlit-sunset-rdslgfrkte0s.vapor-farm-e1') !== false) {
        if (stripos($siteurl, 'gdpr-cookie-consent-banner-cookie-notice-seers') !== false) {
            $shop = isset($_SERVER['X-Shopify-Shop-Domain']) ? $_SERVER['X-Shopify-Shop-Domain'] : $_SERVER['HTTP_X_SHOPIFY_SHOP_DOMAIN'];
        } else {
            $shop = (!empty($_GET['shop'])) ? $_GET['shop'] : "";
        }
        
        $cf_obj = new common_function();
        $us_obj = new User_functions($shop);

        $shop_name = $email = $store_user_id = '';
        $where = array('shop' => $shop);
        $shop_detail_rs = $cf_obj->select_row(config('app.table_user_stores'), 'store_user_id, name, shop, email', $where);

        $shop_detail = (count($shop_detail_rs) > 0 && !empty($shop_detail_rs[0]->email)) ? (array) $shop_detail_rs[0] : ((!$shop_detail_rs->isEmpty()) ? (array) $shop_detail_rs[0] : [] ) ;

        if(!empty($shop_detail)){
            $store_user_id = $shop_detail['store_user_id'];
            $shop_name = $shop_detail['name'];
            $shopdom = $shop_detail['shop'];
            $email = $shop_detail['email'];
            
            //save plugin is deactive on plugins db this plugin
            $cf_obj->plugin_active_inactive($shop_detail, 0);
            
            //remove the js script from html
            // SEND API CALL
                $data = array(
                    'domain' => $shopdom,
                    'user_domain' => $shopdom,
                    'email' => $email,
                    'user_email' => $email,
                    'secret' => '$2y$10$9ygTfodVBVM0XVCdyzEUK.0FIuLnJT0D42sIE6dIu9r/KY3XaXXyS',
                    'platform' => 'shopify',
                    'status'=>'0'
                );

        //         /******* Curl call start *****/
                $curl = curl_init();

                curl_setopt_array($curl, array(
                    CURLOPT_URL => "https://cmp.seersco.com/api/v2/banner-settings",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_SSL_VERIFYPEER => false,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "POST",
                    CURLOPT_POSTFIELDS => $data
                ));

                $response = curl_exec($curl);
                $error_number = curl_errno($curl);
                $error_message = curl_error($curl);
                curl_close($curl);

                $result =  json_decode($response, TRUE);
        }

        // $fields = array(
        //     'status' => '0',
        //     'token' => '',
        //     'app_status' => '0',
        //     'toggle_status' => '0'
        // );
        $where = array('shop' => $shop);
        // $cf_obj->update(config('app.table_user_stores'), $fields, $where);
        $cf_obj->delete(config('app.table_user_stores'), $where);


        return response()->json([
            'status' => 200,
            'data'  => ""
        ]);

    }

    public function ShopUpdate (Request $request) {


        $siteurl = config("app.url");
        // if (stripos($siteurl, 'moonlit-sunset-rdslgfrkte0s.vapor-farm-e1') !== false) {
        if (stripos($siteurl, 'gdpr-cookie-consent-banner-cookie-notice-seers') !== false) {
            $shop = isset($_SERVER['X-Shopify-Shop-Domain']) ? $_SERVER['X-Shopify-Shop-Domain'] : $_SERVER['HTTP_X_SHOPIFY_SHOP_DOMAIN'];
        } else {
            $shop = (!empty($_GET['shop'])) ? $_GET['shop'] : "";
        }
        
        $cf_obj = new common_function($shop);

        $hmac_header = $_SERVER['HTTP_X_SHOPIFY_HMAC_SHA256'];

        /* Here we get all information about customer */
        $shop_update = file_get_contents('php://input');

        /* Todo: checked verify_webhook response(return type ) than set condition according to it */
        $verified = $cf_obj->verify_webhook($shop_update, $hmac_header);

        if (!empty($cf_obj) && $verified && $cf_obj->is_json($shop_update)) {
            /* shop detail array */
            $shop_detail_arr = json_decode($shop_update, TRUE);
            $fields = array(
                'currency' => $shop_detail_arr['currency'],
                'money_format' => $cf_obj->mres($shop_detail_arr['money_format']),
                'owner' => $shop_detail_arr['shop_owner'],
                'shop_plan' => $shop_detail_arr['plan_name'],
                'address1' => $shop_detail_arr['address1'],
                'address2' => $shop_detail_arr['address2'],
                'city' => $shop_detail_arr['city'],
                'country_name' => $shop_detail_arr['country_name'],
                'phone' => $shop_detail_arr['phone'],
                'province' => $shop_detail_arr['province'],
                'zip' => $shop_detail_arr['zip'],
                'timezone' => $shop_detail_arr['timezone'],
                'iana_timezone' => $shop_detail_arr['iana_timezone'],
                'domain' => $shop_detail_arr['domain'],
                'weight_unit' => $shop_detail_arr['weight_unit'],
                );
            
            $selected_field = 'shop_plan, store_user_id';
            $where = array('shop' => $shop);
            $shop_info_db = $cf_obj->select_row(config('app.table_user_stores'), $selected_field, $where);
        }

        return response()->json([
            'status' => 200,
            'data'  => ""
        ]);

    }

    public function AjaxAction (Request $request) {

        $is_bad_shop = 0;
        if (isset($_POST['shop']) && $_POST['shop'] != '') {
            
            
            $uf_obj = new User_functions($_POST['shop']);

            $current_user = $uf_obj->get_store_detail_obj();

            if (!empty($current_user)) {
                /* used for called function (comes from ajax call) */
                if (isset($_POST['method_name']) && $_POST['method_name'] != '') {
                    $response = call_user_func(array($uf_obj, $_POST['method_name']));
                    return json_encode($response);
                }
            } else {
                $is_bad_shop ++;
            }
        } else {
            $is_bad_shop ++;
        }

        if ($is_bad_shop > 0) {
            $response = array('result' => 'fail', 'msg' => 'Opps! Bad request call!', 'code' => '403');
            return json_encode($response);
        }
    }

}