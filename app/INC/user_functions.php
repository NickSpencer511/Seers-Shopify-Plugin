<?php
namespace App\INC;

use App\INC\common_function;
use Carbon\Carbon;
use Str;
use DateTime;
use Illuminate\Support\Facades\Log;

class User_functions extends common_function {

    /**
     * the function "__construct()" automatically starts whenever an object of this class is created,
     */
    public function __construct($shop = '') {
        /* call parent's (common_function) constructor */
        parent::__construct($shop);
    }

    /* When undefined method call that time this function will run */

    public function __call($method, $args) {
        return true;
    }

    public function remove_code($storeuserid = 0, $curshop = '') {
       
        $store_user_id = $this->store_user_id;
        
        if($storeuserid) {
            $store_user_id = $storeuserid;
        }
        
        $response = array('result' => 'fail', 'msg' => 'Something went wrong');
        if (isset($store_user_id) && is_numeric($store_user_id) && $store_user_id > 0) {
            //by Shoaib actually in Post data_key is not coming then I will get the data_key from database of this current user
            $datakey = ((!empty($_POST['data_key'])) ? $_POST['data_key'] : "" );
            $token = '';
            $shop = '';
            
            
            if (empty($datakey)) {
                $selected_field = 'data_key, token, shop';
                $where = array('store_user_id' => $store_user_id);
                $user_store_rs = $this->select_row(config('app.table_user_stores'), $selected_field, $where);

                $user_store = (count($user_store_rs) > 0 && !empty($user_store_rs[0]->shop)) ? (array) $user_store_rs[0] : ((!$user_store_rs->isEmpty()) ? (array) $user_store_rs[0] : [] ) ;


                if (!empty($user_store)) {
                    $datakey = $user_store['data_key'];
                    $token = $user_store['token'];
                    $shop = $user_store['shop'];
                }
            }
            
            
            //$script = '<script data-key="' . $datakey . '" data-name="CookieXray" src="https://cmp.seersco.com/script/cb.js" type="text/javascript"></script>';
            //fix by Shoaib for scripts added in old way start
            $script = '<script(.*?)src="https://cmp.seersco.com/script/cb.js"(.*?)>(.*?)</script>';
            $script2 = '<script(.*?)src="https://seersco.com/script/cb.js"(.*?)>(.*?)</script>';
            
            $themes = $this->prepare_api_condition(array('themes'), array('role' => 'main'), 'GET', '0', '', $curshop);
            if (!empty($themes['body']['themes'])) {
                
            $theme_id = $themes['body']['themes'][0]['id'];

            $url_param_arr = array('asset' => array('key' => 'layout/theme.liquid'));
                $theme_responce = $this->prepare_api_condition(array('themes', $theme_id, 'assets'), $url_param_arr, 'GET', '0', '', $curshop);
            $theme_value = $theme_responce['body']['asset']['value'];

                //$html = str_replace($script, "", $theme_value);
                $html = preg_replace('#'. $script . '#is', '', $theme_value);
                $html = preg_replace('#'. $script2 . '#is', '', $html);
            $url_param_arr = array('asset' => array('key' => 'layout/theme.liquid', 'value' => $html));
                $theme_update = $this->prepare_api_condition(array('themes', $theme_id, 'assets'), $url_param_arr, 'PUT', '0', '', $curshop);
                
            }
            // old way fix end.
            
            // ----- new way remove tags start ---------
            $arrsrc = ['script/cb.js', 'https://localhost/private-apps/script/cbattributes-localhost.js?key=' . $datakey . '&name=CookieXray', 'https://seers-application-assets.s3.amazonaws.com/scripts/cbattributes.js?key=' . $datakey . '&name=CookieXray'];
            $cbattrjspath = 'https://seers-application-assets.s3.amazonaws.com/scripts/cbattributes.js';
        
            if ($_SERVER['SERVER_NAME'] == 'localhost')
                $cbattrjspath = 'https://localhost/private-apps/script/cbattributes-localhost.js';


            //get all avialable tags
            $allscriptags = $this->prepare_api_condition(array('script_tags'), array(), 'GET', '0', $token, $shop);

            //print_r($allscriptags);

            if(!empty($allscriptags['body']) && !empty($allscriptags['body']['script_tags'])) {

                foreach ($allscriptags['body']['script_tags'] as $thescript) {

                    if (stripos($thescript['src'], $arrsrc[0]) !== false) {
                        //remove the script
                        $scriptdel = $this->prepare_api_condition(array('script_tags', $thescript['id']), array(), 'DELETE', '0', $token, $shop);
                    }else if (stripos($thescript['src'], $arrsrc[0]) !== false) {
                        //remove the script
                        $scriptdel = $this->prepare_api_condition(array('script_tags', $thescript['id']), array(), 'DELETE', '0', $token, $shop);
                    } else if (stripos($thescript['src'], $cbattrjspath) !== false && strcasecmp($thescript['src'], $arrsrc[1]) !== 0) {
                        //remove the script
                        $scriptdel = $this->prepare_api_condition(array('script_tags', $thescript['id']), array(), 'DELETE', '0', $token, $shop);
                    } else if (stripos($thescript['src'], $arrsrc[2]) !== false) {
                        //remove the script
                        $scriptdel = $this->prepare_api_condition(array('script_tags', $thescript['id']), array(), 'DELETE', '0', $token, $shop);
                    }
                }


            }
            // ----- new way remove tags end ---------
            
            
            $response = array('result' => 'success', 'msg' => 'Code Remove successfully.');
        }
        return $response;
    }



    public function change_appStatus(){

        $cf_obj = new common_function();
        $shop = isset($_POST['shop']) ? $_POST['shop'] : $_GET['shop'];
        $store_user_id = $this->store_user_id;
        $data_status = $_POST['data_status'];
        $user_domain   = $_POST['user_name'];
        $user_email    = $_POST['user_email'];

        $user_key      = $_POST['data_key'];
        //live cdn script baseurl
        $scriptbaseurl = "https://cdn.seersco.com/";

        if($data_status=='true')
        {
            $data_status =  '1';
        }else{
            $data_status =  '0';
        }


        $selected_field = '*';
        $where = array('shop' => $shop,'store_user_id' => $store_user_id);
        $is_store_exist_rs = $cf_obj->select_row(config('app.table_user_stores'), $selected_field, $where);

        $is_store_exist = (count($is_store_exist_rs) > 0 && !empty($is_store_exist_rs[0]->email)) ? (array) $is_store_exist_rs[0] : ((!$is_store_exist_rs->isEmpty()) ? (array) $is_store_exist_rs[0] : [] ) ;

        $already_toggle_status = $is_store_exist['toggle_status'];
        $domain = $is_store_exist['domain'];
        $email  = $is_store_exist['email'];
        $token  = $is_store_exist['token'];
        $shop   = $is_store_exist['shop'];
        if(!empty($is_store_exist)){
            // SEND API CALL
            $data = array(
                'domain' => $domain,
                'user_domain' => $domain,
                'email' => $email,
                'user_email' => $email,
                'secret' => '$2y$10$9ygTfodVBVM0XVCdyzEUK.0FIuLnJT0D42sIE6dIu9r/KY3XaXXyS',
                'platform' => 'shopify',
                'status'=>$data_status,
            );

    //         /******* Curl call start *****/
            $curl = curl_init();

            curl_setopt_array($curl, array(
                //CURLOPT_URL => "https://seersco.com/api/banner-settings",
                CURLOPT_URL => $this->apibaseurl . "banner-settings",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => $data
            ));

            $response = curl_exec($curl);
            $error_number = curl_errno($curl);
            $error_message = curl_error($curl);
            curl_close($curl);

            $result =  json_decode($response, TRUE);
           
            //var_dump($result);
            //exit;
            //by Shoaib in reponse there is no element of banner_enable
            // {"key":"$2y$10$ZtDil0sCM95w..QVVdqOielWh7YRbySFOPDgzR.K4iukb5I7ewF4G","status":0,"message":"success"}

          //$banner_status = $result['banner_enable'];
          $banner_status = ((!empty($result['banner_enable'])) ? $result['banner_enable'] : ((isset($result['status'])) ? $result['status'] : $already_toggle_status ) );
          
          if(!empty($result['key'])){
                $user_key = $result['key']; 
           }else{
                $user_key = "";
           }

           if(!empty($result['cdnbaseurl']))
            $scriptbaseurl = $result['cdnbaseurl'];
          
            //$banner_status = '1';
           $jsonresponse = array('result' => 'fail', 'msg' => 'Something went wrong');
                
            if($banner_status=='1'){
                $jsonresponse = array('result' => 'success', 'key'=>$user_key, 'msg' => "<p><span class ='banner-tick'></span>Banner is enabled on your store. <br> <span style='margin-left:18px;'></span>Please refresh your store home page to see the effect.</p>");
                $this->snippest_insert($shop, $token, $domain, $email);
                $this->insertConsentTrackingScript($shop, $token);

                
          }else{
              $jsonresponse = array('result' => 'success', 'key'=>$user_key, 'msg' => 'Banner is disabled on your store');
                $this->remove_code();
            }
            /** Update Banner Status */
            $this->updateToogelStatus($cf_obj, $shop, $banner_status,$user_domain,$user_email,$user_key);

            if (!empty($result['message']) && strcasecmp($result['message'], 'success') === 0 || $banner_status === '0' || $banner_status === 0)
            {
                return $jsonresponse;
            } else if (!empty($result['message'])) {
                $jsonresponse['errormsg'] = $result['message'];
                return $jsonresponse;
            } else if (!empty($result['errors'])) {
                $errormessages = "";
                if( !empty($result['errors']['email']) ) {
                    $errormessages = $result['errors']['email'][0];
                }

                //domain
                if( !empty($result['errors']['domain']) ) {
                    $errormessages = (($errormessages) ? "<br>" : "" ) + $result['errors']['domain'][0];
                }

                $jsonresponse['errormsg'] = $errormessages;
                return $jsonresponse;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function get_user_data() {
        $cf_obj = new common_function();

        $shop = isset($_POST['shop']) ? $_POST['shop'] : $_GET['shop'];
        $domain = isset($_POST['domain']) ? $_POST['domain'] : null;
        $email = isset($_POST['email']) ? $_POST['email'] : null;
        $token = isset($_POST['token']) ? $_POST['token'] : null;
    
        if (!$domain || !$email || !$shop) {
            return [
                'status' => 'error',
                'message' => 'Domain, email, and shop fields are required.'
            ];
        }
    
        $data = array(
            'domain' => $domain,
            'email' => $email,
            'secret' => '$2y$10$9ygTfodVBVM0XVCdyzEUK.0FIuLnJT0D42sIE6dIu9r/KY3XaXXyS',
            'lang' => 'en_US',
            'platform' => 'shopify',
            'token' => $token
        );
    
        $referer_url = "https://" . $domain;
    
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->apibaseurl . "get-shopify-banner-settings", 
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_SSL_VERIFYPEER => false, 
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Accept: application/json',
                'Referer: ' . $referer_url
            ),
        ));
    
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
    
        if ($err) {
            return [
                'status' => 'error',
                'message' => 'cURL Error: ' . $err
            ];
        }
    
        $api_response = json_decode($response, true);
    
        if (json_last_error() === JSON_ERROR_NONE) {
            return [
                'status' => 'success',
                'message' => 'Changes Saved Successfully',
                'data' => $api_response
            ];
        } else {
            return [
                'status' => 'error',
                'message' => 'Invalid JSON response from API',
                'raw_response' => $response
            ];
        }
    }

    public function update_user_data() {
        $cf_obj = new common_function();
    
        $shop = isset($_POST['shop']) ? $_POST['shop'] : $_GET['shop'];
        $domain = isset($_POST['domain']) ? $_POST['domain'] : null;
        $email = isset($_POST['email']) ? $_POST['email'] : null;
        $data = isset($_POST['data']) ? $_POST['data'] : null;
        $token = isset($_POST['token']) ? $_POST['token'] : null;
    
        $payload = [
            'data' => $data,
            'token' => $token
        ];
    
    
        $referer_url = "https://" . $domain;
    
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->apibaseurl . "update-shopify-banner-settings",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($payload), 
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Accept: application/json',
                'Referer: ' . $referer_url
            ),
        ));
    
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
    
        if ($err) {
            return [
                'status' => 'error',
                'message' => 'cURL Error: ' . $err
            ];
        }
    
    
        $api_response = json_decode($response, true);
    
        if (json_last_error() === JSON_ERROR_NONE) {
            return [
                'status' => 'success',
                'message' => 'Changes Saved Successfully',
                'data' => $api_response
            ];
        } else {
            return [
                'status' => 'error',
                'message' => 'Invalid JSON response from API',
                'raw_response' => $response
            ];
        }
    }
    
    
    
    public function updateToogelStatus($cf_obj, $shop, $banner_status,$user_domain,$user_email,$user_key){


        $shop_details = array(
            'status'=>'1',
            'updated_on'=>date('Y-m-d H:i:s'),
            'toggle_status'=>$banner_status,
            'domain'=>$user_domain,
            'email'=>$user_email,
            'data_key'=>$user_key
        );

        $where = array('shop' => $shop,'store_user_id' => $this->store_user_id);
        $last_id = $cf_obj->update(config('app.table_user_stores'), $shop_details, $where);
    }


    public function get_customize_redirect_url() {
        $domain = $_POST['domain'] ?? '';
        $email = $_POST['email'] ?? '';
        $shop = $_POST['shop'] ?? '';
        $domain_id = $_POST['domain_id'] ?? '';
        $user_id = $_POST['user_id'] ?? '';
        $tab_name = $_POST['tab_name'] ?? '';
        $sub_tab_name = $_POST['sub_tab_name'] ?? '';

         Log::info('CMP API Response domain' , ['response' => $domain]);

        $response = [];
        if (!$domain || !$email || !$shop) {
            return ['status' => 'error', 'message' => 'Missing domain, email, or shop.'];
        }
        if (empty($domain_id) || empty($user_id)) {
            $cf = new \App\INC\common_function($shop);
            $response = $cf->get_data_key($domain, $email);
            Log::info('CMP API Response', ['response' => $response]);

            $domain_id = $response['domain_id'] ?? '';
            $user_id = $response['user_id'] ?? '';
            $access_token = $response['access_token'] ?? '';

            if ($domain_id && $user_id && $access_token) {
                $fields = [
                    'access_token' => $access_token,
                    'domain_id' => $domain_id,
                    'user_id' => $user_id,
                ];
                $where = ['shop' => $shop];
                $last_id = $this->update(config('app.table_user_stores'), $fields, $where);
            }
        }
        if (!empty($response['access_token']) && !empty($response['domain_id'])) {
            $url = 'https://app.seersco.com/token/?access_token=' . urlencode($response['access_token']) . '&domain_id=' . urlencode($response['domain_id']);
            if (!empty($tab_name)) {
                $url .= '&tab_name=' . urlencode($tab_name);
            }
            if (!empty($sub_tab_name)) {
                $url .= '&sub_tab_name=' . urlencode($sub_tab_name);
            }
            return ['status' => 'success', 'redirect_url' => $url];
        }
        // if (!empty($response['access_token']) && !empty($response['domain_id'])) {
        //     $url = 'http://localhost:8080/token/?access_token=' . urlencode($response['access_token']);
        //     return ['status' => 'success', 'redirect_url' => $url];
        // }

        error_log("CMP response: " . print_r($response, true));

        return ['status' => 'error', 'message' => 'Invalid response from CMP API'];
    }

}
