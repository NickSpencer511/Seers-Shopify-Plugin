<?php
namespace App\INC;

use App\INC\DB_Class;
use Carbon\Carbon;
use Str;
use DateTime;
use DB;
use Illuminate\Support\Facades\Log;

class common_function {

    protected $current_store_obj = null;
    protected $store_user_id = null;
    protected $store_name = null;
    protected $store_email = null;
    protected $app_status = null;
    protected $app_plan = null;
    protected $is_charge_approve = '';
    protected $money_format = null;
    protected $currency = null;
    protected $shop_plan = '';
    protected $timezone = '';
    public $db_connection = null;
    // public $apisecrekkey = '$2y$10$9ygTfodVBVM0XVCdyzEUK.0FIuLnJT0D42sIE6dIu9r/KY3XaXXyS';
    // public $apisecrekkey = '';
    // protected $apibaseurl = "https://cmp.seersco.com/api/v2/";
    // protected $apibaseurl = "http://127.0.0.1:2000/api/v2/";
    // protected $apibaseurl = '';
    protected $last_query = '';

    public function __construct($shop = '') {
        // $this->apibaseurl   = env('SEERS_API_BASE_URL', 'https://cmp.seersco.com/api/v2/');
        // $this->apisecrekkey = env('SEERS_API_SECRET', '');
        $this->apibaseurl   = config('app.seers_api_base_url');
        $this->apisecrekkey = config('app.seers_api_secret');
        /*if ($this->db_connection == null) {
            $db_connection = new DB_Class();
            $this->db_connection = $GLOBALS['conn'];
        }*/

        if ($shop != '') {
            $this->set_user_data($shop);
        }
    }

    public function set_user_data($shop) {
        $selected_field = '*';
        $where = array('shop' => $shop, 'status' => '1');
        $user_store_arrobjs = $this->select_row(config('app.table_user_stores'), $selected_field, $where);

        $user_store = (count($user_store_arrobjs) > 0 && !empty($user_store_arrobjs[0]->store_user_id)) ? (array) $user_store_arrobjs[0] : ((!$user_store_arrobjs->isEmpty()) ? (array) $user_store_arrobjs[0] : [] ) ;

        if (!empty($user_store)) {
            $this->current_store_obj = $user_store;
            $this->store_user_id = $user_store['store_user_id'];
            $this->store_name = $user_store['name'];
            $this->store_email = $user_store['email'];
            $this->app_status = ((!empty($user_store['app_status'])) ? $user_store['app_status'] : "");
            $this->app_plan = ((!empty($user_store['app_plan'])) ? $user_store['app_plan'] : "");
            $this->shop_plan = $user_store['shop_plan'];
            $this->money_format = $user_store['money_format'];
            $this->currency = $user_store['currency'];
            $this->charge_approve = ((!empty($user_store['charge_approve'])) ? $user_store['charge_approve'] : "");
            $this->timezone = $user_store['iana_timezone'];
        }
    }

    public function get_store_detail_obj() {
        if ($this->current_store_obj != null) {
            return $this->current_store_obj;
        }
    }

    public function get_store_user_id() {
        return $this->store_user_id;
    }

    public function get_store_name() {
        return $this->store_name;
    }

    public function get_store_email() {
        return $this->store_email;
    }

    public function get_app_status() {
        return $this->app_status;
    }

    public function get_app_plan() {
        return $this->app_plan;
    }

    public function get_shop_plan() {
        return $this->shop_plan;
    }

    public function get_is_charge_approve() {
        return $this->charge_approve;
    }

    public function get_currency() {
        return $this->currency;
    }

    public function get_timezone() {
        return $this->timezone;
    }

    function insert($table, $fields) {
        $columns = $values = array();
        foreach ($fields as $key => $value) {
            $columns[] = $key;
            $values[] = $value;
        }
        /*$insert_query = "INSERT INTO $table" . ' (' . implode(',', $columns) . ") VALUES('" . implode("','", $values) . "')";
        $this->query($insert_query);*/

        $id = DB::table($table)->insertGetId($fields);

        return $id;
    }

    function insert_on_duplicate_update($table, $fields) {
        $insert_columns = $values = array();
        $update_columns = '';
        foreach ($fields as $key => $value) {
            $insert_columns[] = $key;
            $values[] = $value;
            if ($key != 'created_on') {
                $update_columns .= $key . "='$value',";
            }
        }
        $insert_query = "INSERT INTO $table" . ' (' . implode(',', $insert_columns) . ") VALUES('" . implode("','", $values) . "')";
        $update_query = "UPDATE " . rtrim($update_columns, ",");

        $insert_on_duplicate_update_query = $insert_query . " ON DUPLICATE KEY " . $update_query . ";";

        $this->query($insert_on_duplicate_update_query);

        return $this->db_connection->insert_id;
    }

    function update($table, $fields, $where) {
        $update_query = "UPDATE $table SET ";
        $columns = '';
        foreach ($fields as $key => $value) {
            $columns .= $key . "='$value',";
        }

        $wherestr = $this->prepare_where_condition($where);

        if ($wherestr == '') {
            echo "<pre>update without where condition not allowed</pre>";
            exit;
        }

        $update_query .= rtrim($columns, ",") . " $wherestr ";

        $update_query .= " ;";

        $wherecolname = key ($where);

        return DB::table($table)
            ->where($wherecolname, $where[$wherecolname])
            ->update($fields);
    }

    function delete($table, $where, $limit = NULL) {

        $wherestr = $this->prepare_where_condition($where);

        if ($wherestr == '') {
            echo "<pre>delete without where condition not allowed</pre>";
            exit;
        }

        $delete_query = "DELETE FROM $table $wherestr ";

        if (isset($limit) && is_numeric($limit) && $limit > 0) {
            $delete_query .= "LIMIT $limit";
        }

        $delete_query .= " ;";

        $wherecolname = key ($where);
        //return $this->query($delete_query);
        return DB::table($table)->where($wherecolname, $where[$wherecolname])->delete();
    }

    function select_row($table, $selected_field = '*', $where = NULL) {

        $wherestr = $this->prepare_where_condition($where);

        $sql = "SELECT " . $selected_field . "  FROM " . $table . " " . $wherestr . " LIMIT 1;";

        /*$query_resource = $this->query($sql);

        $table_data = array();

        if ($query_resource && $query_resource->num_rows > 0) {
            $table_data = $query_resource->fetch_assoc();
        }*/

        $wherecolname = $where ? array_keys ($where) : [];
        //$user_store_res =DB::table($table)->where($wherecolname[0], $where[$wherecolname])->where('status', '1')->get();
        $user_store_res =DB::table($table);

        foreach ($wherecolname as $ind => $wherekey) {
            $user_store_res =$user_store_res->where($wherekey, $where[$wherekey]);
        }

        $user_store_res = $user_store_res->get();

        $table_data = $user_store_res;

        return $table_data;
    }

    function select_result($table, $selected_field = '*', $where = NULL, $orderBy = NULL, $groupBy = NULL, $limit = NULL, $offset = NULL) {

        $where = $this->prepare_where_condition($where);

        $sql = "SELECT " . $selected_field . "  FROM " . $table . " " . $where;

        if (isset($groupBy)) {
            $sql .= " GROUP BY " . $groupBy . " ";
        }
        if (isset($orderBy)) {
            $sql .= " ORDER BY " . $orderBy . " ";
        }
        if (isset($offset) && isset($limit)) {
            $sql .= " LIMIT  " . $offset . "," . $limit;
        }
        if (isset($limit) && !isset($offset)) {
            $sql .= " LIMIT  " . $limit;
        }
        $sql .= ";";

        $query_resource = $this->query($sql);

        $table_data = array();

        if ($query_resource && $query_resource->num_rows > 0) {
            while ($row = $query_resource->fetch_assoc()) {
                $table_data[] = $row;
            }
        }
        return $table_data;
    }

    function prepare_where_condition($where_condition) {
        if (!isset($where_condition) || $where_condition == '') {
            $where_condition = '';
        } elseif (is_array($where_condition) && !empty($where_condition)) {
            $where = array();
            foreach ($where_condition as $field => $value) {
                $where[] = "$field = '$value'";
            }
            $where_condition = " WHERE " . implode(" AND ", $where);
        } else if (isset($where_condition) && is_string($where_condition)) {
            $where_condition = " WHERE " . $where_condition;
        }
        return $where_condition;
    }

    function get_record_with_join($table, $selected_field = '', $where = NULL, $orderBy = NULL, $groupBy = NULL, $limit = NULL, $offset = NULL, $join_arr = array()) {
        $sql = "SELECT " . $selected_field . "  FROM " . $table . "";
        if (!empty($join_arr)) {
            foreach ($join_arr as $join) {
                if ($join['join_type'] == '') {
                    $sql .= " INNER JOIN " . $join['table'] . " ON " . $join['join_table_id'] . " = " . $join['from_table_id'];
                } else {
                    $sql .= " " . $join['join_type'] . " " . $join['table'] . " ON " . $join['join_table_id'] . " = " . $join['from_table_id'];
                }
            }
        }

        $where = $this->prepare_where_condition($where);

        if ($where != '') {
            $sql .= " " . $where;
        }

        if (isset($groupBy)) {
            $sql .= " GROUP BY " . $groupBy . " ";
        }
        if (isset($orderBy)) {
            $sql .= " ORDER BY " . $orderBy . " ";
        }
        if (isset($offset) && isset($limit)) {
            $sql .= " LIMIT  " . $offset . "," . $limit;
        }
        if (isset($limit) && !isset($offset)) {
            $sql .= " LIMIT  " . $limit;
        }
        $sql .= ";";

        $query_resource = $this->query($sql);
        $table_data = array();

        if ($query_resource && $query_resource->num_rows > 0) {
            while ($row = $query_resource->fetch_assoc()) {
                $table_data[] = $row;
            }
        }
        return $table_data;
    }

    function query($query) {
        $this->last_query = $query;

        $query_resource_obj = $this->db_connection->query($query);

        /* if mode is dev and query getting error than below block display the query
         * and stop execution of script
         */
        /*if (!$query_resource_obj && MODE == 'dev') {
            echo "<pre>" . mysqli_error($this->db_connection) . "<br>" . "\n";
            print_r($query);
            echo "\n" . "<br>" . "</pre>";
            exit;
        }*/

        return $query_resource_obj;
    }

    function get_total_record($table, $where = NULL, $group_by = NULL, $join_arr = array()) {
        $where = $this->prepare_where_condition($where);
        $count = "COUNT(*)";
        if (isset($group_by)) {
            $count = "COUNT(DISTINCT  $group_by)";
        }
        $sql = "SELECT $count as total_row FROM $table";

        if (!empty($join_arr)) {
            foreach ($join_arr as $join) {
                if ($join['join_type'] == '') {
                    $sql .= " INNER JOIN " . $join['table'] . " ON " . $join['join_table_id'] . " = " . $join['from_table_id'];
                } else {
                    $sql .= " " . $join['join_type'] . " " . $join['table'] . " ON " . $join['join_table_id'] . " = " . $join['from_table_id'];
                }
            }
        }
        $sql .= ' ' . $where . ';';
        $mysql_resource = $this->query($sql);
        if ($mysql_resource) {
            return $mysql_resource->fetch_row()['0'];
        } else {
            return '0';
        }
    }

    /**
     * Last query get
     * @return (string)
     */
    public function last_query() {
        return $this->last_query;
    }

    public function verify_webhook($data, $hmac_header) {
        $calculated_hmac = base64_encode(hash_hmac('sha256', $data, config('app.shopify_secret'), true));
        return ($hmac_header == $calculated_hmac);
    }

    function prepare_api_condition($api_main_url_arr, $url_param_arr = array(), $method = 'GET', $is_object = 1, $token = '', $shop = '', $request_headers = array()) {
        if ($this->current_store_obj != NULL) {
            $shop_info = $this->current_store_obj;
            $token = $shop_info['token'];
            $shop = $shop_info['shop'];
        }
        $shopify_api_version = '';
        $date = strtotime('-1 day', strtotime(date('Y-m-d')));
        $month = date('m', $date);
        $year = date('Y', $date);

        // switch ($month) {
        //     case $month <= 3:
        //         $shopify_api_version = $year . '-01';
        //         break;
        //     case $month <= 6:
        //         $shopify_api_version = $year . '-04';
        //         break;
        //     case $month <= 9:
        //         $shopify_api_version = $year . '-07';
        //         break;
        //     case $month <= 12:
        //         $shopify_api_version = $year . '-10';
        //         break;
        // }
        if ($month <= 3) {
            $shopify_api_version = $year . '-01';
        } elseif ($month <= 6) {
            $shopify_api_version = $year . '-04';
        } elseif ($month <= 9) {
            $shopify_api_version = $year . '-07';
        } else {
            $shopify_api_version = $year . '-10';
        }
        $api_main_url_arr = ($token != '') ? array_merge(array('/admin/api/' . $shopify_api_version), $api_main_url_arr) : array_merge(array('/admin'), $api_main_url_arr);
        $api_main_url = ($token != '') ? implode('/', $api_main_url_arr) . '.json' : implode('/', $api_main_url_arr);
        return $this->api_call($token, $shop, $api_main_url, $is_object, $request_headers, $method, $url_param_arr);
    }

    function api_call($token, $shop, $api_endpoint, $is_object, $request_headers, $method = 'GET', $query = array()) {
        $url = "https://" . $shop . $api_endpoint;
        if (!empty($query) && !is_null($query) && in_array($method, array('GET', 'DELETE'))) {
            $url = $url . "?" . http_build_query($query);
        } else {
            $url = $url;
        }

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 3);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_USERAGENT, 'ohShopify-php-api-client');
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);

        $request_headers[] = "";

        if (!is_null($token) && $token != '')
            $request_headers[] = "X-Shopify-Access-Token: " . $token;
        curl_setopt($ch, CURLOPT_HTTPHEADER, $request_headers);
        if ($method != 'GET' && in_array($method, array('POST', 'PUT'))) {
            if (is_array($query))
                $query = http_build_query($query);

            curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
        }

        $response = curl_exec($ch);
        if (count(preg_split("/\r\n\r\n|\n\n|\r\r/", $response)) == 3) {
            list(, $headers, $body) = preg_split("/\r\n\r\n|\n\n|\r\r/", $response, 3);
        } else {
            list($headers, $body) = preg_split("/\r\n\r\n|\n\n|\r\r/", $response, 2);
        }

        $header_lines = preg_split("/\r\n|\n|\r/", $headers);
        $header = array();
        if (count(explode(' ', trim(array_shift($header_lines)), 3)) == '3') {
            list(, $header['http_status_code'], $header['http_status_message']) = explode(' ', trim(array_shift($header_lines)), 3);
        } else {
            list($header['http_status_code'], $header['http_status_message']) = explode(' ', trim(array_shift($header_lines)), 2);
        }

        foreach ($header_lines as $header_line) {
            list($name, $value) = explode(':', $header_line, 2);
            $name = strtolower($name);
            $header[$name] = str_replace(array('<', '>'), '', trim($value));
        }

        $error_number = curl_errno($ch);
        $error_message = curl_error($ch);
        curl_close($ch);

        if ($error_number) {
            return $error_message;
        } else {
            if ($is_object) {
                return array('headers' => $header, 'body' => json_decode($body));
            } else {
                return array('headers' => $header, 'body' => json_decode($body, TRUE));
            }
        }
    }

    public function is_json($args) {
        json_decode($args);
        return (json_last_error() === JSON_ERROR_NONE);
    }

    public function get_data_key($domain, $email) {
        $data = array(
            'domain' => $domain,
            'email' => $email,
            'user_email' => $email,
            'secret' => $this->apisecrekkey,
            'platform' => 'shopify',
        );

        $curl = curl_init();

        curl_setopt_array($curl, array(
            //CURLOPT_URL => "https://seersco.com/api/get-key-for-shopify",
            CURLOPT_URL => $this->apibaseurl . "get-key-for-shopify",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            // CURLOPT_TIMEOUT => 0,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_TIMEOUT => 8,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $data
        ));

        $response = curl_exec($curl);
        if (curl_errno($curl) || empty($response)) {
            \Log::error('get_data_key failed: ' . curl_error($curl));
            curl_close($curl);
            return []; // fail gracefully
        }

        $error_number = curl_errno($curl);
        $error_message = curl_error($curl);
        curl_close($curl);

        return json_decode($response, TRUE);
    }

    // public function get_user_data($domain, $email) {
    //     $data = array(
    //         'domain' => $domain,
    //         'email' => $email,
    //         'user_email' => $email,
    //         'secret' => $this->apisecrekkey,
    //         'platform' => 'shopify',
    //     );

    //     $curl = curl_init();

    //     curl_setopt_array($curl, array(
    //         //CURLOPT_URL => "https://seersco.com/api/get-key-for-shopify",
    //         CURLOPT_URL => $this->apibaseurl . "get-banner-settings",
    //         CURLOPT_RETURNTRANSFER => true,
    //         CURLOPT_ENCODING => "",
    //         CURLOPT_MAXREDIRS => 10,
    //         CURLOPT_TIMEOUT => 0,
    //         CURLOPT_FOLLOWLOCATION => true,
    //         CURLOPT_SSL_VERIFYPEER => false,
    //         CURLOPT_SSL_VERIFYHOST => false,
    //         CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    //         CURLOPT_CUSTOMREQUEST => "POST",
    //         CURLOPT_POSTFIELDS => $data
    //     ));

    //     $response = curl_exec($curl);
    //     $error_number = curl_errno($curl);
    //     $error_message = curl_error($curl);
    //     curl_close($curl);

    //     return json_decode($response, TRUE);
    // }

    // public function snippest_insert($shop, $token, $domain, $email) {

    //     $selected_field = 'data_key';
    //     $where = array('shop' => $shop, 'status' => '1');
    //     $store_row_rs = $this->select_row(config('app.table_user_stores'), $selected_field, $where);

    //     $store_row = (count($store_row_rs) > 0 && !empty($store_row_rs[0]->email)) ? (array) $store_row_rs[0] : ((!$store_row_rs->isEmpty()) ? (array) $store_row_rs[0] : [] ) ;

    //     $old_script = '';
    //     $datakey = '';
    //     //live cdn script baseurl
    //     $scriptbaseurl = "https://cdn.seersco.com/";

    //     if(!empty($store_row)){
    //         $datakey = $store_row['data_key'];
    //     }

    //     $response = $this->get_data_key($domain, $email);


    //     if(!empty($response['key']))
    //         $datakey = $response['key'];
    //     else
    //         $datakey = "";

    //     if(!empty($response['access_token']))
    //         $access_token = $response['access_token'];
    //     else
    //         $access_token = "";

    //     if(!empty($response['domain_id']))
    //         $domain_id = $response['domain_id'];
    //     else
    //         $domain_id = "";

    //     if(!empty($response['user_id']))
    //         $user_id = $response['user_id'];
    //     else
    //         $user_id = "";

    //     if(!empty($response['cdnbaseurl']))
    //         $scriptbaseurl = $response['cdnbaseurl'];

    //     $fields['data_key'] = $datakey;
    //     $fields['access_token'] = $access_token;
    //     $fields['domain_id'] = $domain_id;
    //     $fields['user_id'] = $user_id;

    //     $where = array('shop' => $shop);
    //     $last_id = $this->update(config('app.table_user_stores'), $fields, $where);

    //     //$arrsrc = ['https://cmp.seersco.com/script/cb.js', 'https://seers-application-assets.s3.amazonaws.com/scripts/cbattributes.js?key=' . $datakey . '&name=CookieXray'];

    //     $cbattrjspath = 'https://seers-application-assets.s3.amazonaws.com/scripts/cbattributes.js';

    //     if ($_SERVER['SERVER_NAME'] == 'localhost')
    //         $cbattrjspath = 'https://localhost/private-apps/script/cbattributes-localhost.js';

    //     if(!empty($response['user_id']) && !empty($response['domain_id'])) {
    //         //$arrsrc = [ $scriptbaseurl . 'banners/' . $response['user_id'] . '/' . $response['domain_id'] . '/cb.js', $cbattrjspath . '?key=' . $datakey . '&name=CookieXray'];
    //         $arrsrc = [ $scriptbaseurl . 'banners/' . $response['user_id'] . '/' . $response['domain_id'] . '/cb.js' . '?param=' . $datakey . '&name=CookieXray'];
    //     } else {
    //         //$arrsrc = [ 'https://seerscophp8.backend/script/cb.js', $cbattrjspath . '?key=' . $datakey . '&name=CookieXray'];
    //         $arrsrc = [ 'https://seerscophp8.backend/script/cb.js' . '?param=' . $datakey . '&name=CookieXray'];
    //     }


    //     $arrscriptexist = [false, false];


    //     //get all avialable tags
    //     $allscriptags = $this->prepare_api_condition(array('script_tags'), array(), 'GET', '0', $token, $shop);

    //     //print_r($allscriptags);

    //     if(!empty($allscriptags['body']) && !empty($allscriptags['body']['script_tags'])) {

    //         foreach ($allscriptags['body']['script_tags'] as $thescript) {

    //             if (strcasecmp($thescript['src'], $arrsrc[0]) === 0) {
    //                 $arrscriptexist[0] = true;
    //             } else if (strcasecmp($thescript['src'], $arrsrc[1]) === 0) {
    //                 $arrscriptexist[1] = true;
    //             } else if (stripos($thescript['src'], $cbattrjspath) !== false && strcasecmp($thescript['src'], $arrsrc[1]) !== 0) {
    //                 $arrscriptexist[1] = false;
    //                 //remove the script
    //                 $scriptdel = $this->prepare_api_condition(array('script_tags', $thescript['id']), array(), 'DELETE', '0', $token, $shop);
    //             }
    //         }


    //     }

    //     foreach ($arrsrc as $sitind => $sitesrc) {

    //         if (!$arrscriptexist[$sitind]) {

    //             //add this src in scripts
    //             $scriptinsert = $this->prepare_api_condition(array('script_tags'), array('script_tag' => array( "event"=>"onload", "src"=>$sitesrc, "display_scope" => "online_store","attributes" => array("data-shopify-cmp" => ""))), 'POST', '0', $token, $shop);

    //         }

    //     }


    //     // $responseUser = $this->get_user_data($domain, $email);

    //     // echo "<pre>";
    //     // print_r($responseUser);
    //     // echo "</pre>";

    // }

    public function activate_app_embed($shop, $token, $domain, $email) {

        // Step 1 - Get keys from Seers API
        $response = $this->get_data_key($domain, $email);

        $datakey       = $response['key'] ?? '';
        $domain_id     = $response['domain_id'] ?? '';
        $user_id       = $response['user_id'] ?? '';
        $scriptbaseurl = $response['cdnbaseurl'] ?? "https://cdn.seersco.com/";
        $access_token  = $response['access_token'] ?? '';

        // Step 2 - Build cb.js URL
        if (!empty($user_id) && !empty($domain_id)) {
            $cb_js_url = $scriptbaseurl . 'banners/' . $user_id . '/' . $domain_id
                . '/cb.js?param=' . $datakey . '&name=CookieXray';
        } else {
            $cb_js_url = 'https://cdn.seersco.com/banners/default/default/cb.js?param='
                . $datakey . '&name=CookieXray';
        }

        // Step 3 - Save to DB
        $fields = [
            'data_key'     => $datakey,
            'domain_id'    => $domain_id,
            'user_id'      => $user_id,
            'cb_js_url'    => $cb_js_url,
            'access_token' => $access_token,
        ];
        $this->update(config('app.table_user_stores'), $fields, ['shop' => $shop]);
        $this->update_shop_metafields($shop, $token, $datakey, $domain_id, $user_id, $cb_js_url);

        // Step 4 - Get current active theme
        $themes = $this->prepare_api_condition(
            ['themes'], ['role' => 'main'], 'GET', '0', $token, $shop
        );
        $theme_id = $themes['body']['themes'][0]['id'] ?? null;

        if (!$theme_id) {
            return ['status' => 'error', 'message' => 'No active theme found'];
        }

        // Step 5 - Build Theme Editor deep link using Client ID (not extension UUID)
        $client_id = config('app.shopify_apikey');

        $editor_url = "https://{$shop}/admin/themes/{$theme_id}/editor"
            . "?context=apps"
            . "&appEmbed={$client_id}%2Fcookie_banner";

        // \Log::info('activate_app_embed - editor_url: ' . $editor_url);

        return [
            'status'     => 'success',
            'message'    => 'App embed ready',
            'theme_id'   => $theme_id,
            'cb_js_url'  => $cb_js_url,
            'editor_url' => $editor_url,
        ];
    }

    public function deactivate_app_embed($shop, $token) {

        // Step 1 - Get current active theme
        $themes = $this->prepare_api_condition(
            ['themes'], ['role' => 'main'], 'GET', '0', $token, $shop
        );
        $theme_id = $themes['body']['themes'][0]['id'] ?? null;

        if (!$theme_id) {
            return ['status' => 'error', 'message' => 'No active theme found'];
        }

        // Step 2 - Build Theme Editor deep link for merchant to disable manually
        // Same restriction as activate - Shopify blocks programmatic writes
        // Direct the merchant to Theme Editor to toggle off
        $client_id = config('app.shopify_apikey');

        $editor_url = "https://{$shop}/admin/themes/{$theme_id}/editor"
            . "?context=apps"
            . "&appEmbed={$client_id}%2Fcookie_banner";

        // \Log::info('deactivate_app_embed - editor_url: ' . $editor_url);

        return [
            'status'     => 'success',
            'message'    => 'App embed deactivated - merchant must disable in Theme Editor',
            'theme_id'   => $theme_id,
            'editor_url' => $editor_url,
        ];
    }

   public function update_theme_app_extension($shop, $token, $new_data_key, $new_domain_id, $new_user_id, $new_cb_js_url) {

    Log::info('update_theme_app_extension - START', [
        'shop'          => $shop,
        'token_preview' => substr($token, 0, 30),
        'new_data_key'  => substr($new_data_key, 0, 30)
    ]);

    // Always use fresh instance to get latest token from DB
    $fresh_cf = new common_function($shop);
    $store    = $fresh_cf->get_store_detail_obj();
    $token    = $store['token'] ?? $token;

    Log::info('update_theme_app_extension - using fresh token: ' . substr($token, 0, 30));

    // Step 1 — Get active theme ID
    $themes      = $fresh_cf->prepare_api_condition(
        ['themes'], ['role' => 'main'], 'GET', '0', $token, $shop
    );
    $themes_body = json_decode(json_encode($themes['body']), true);
    $theme_id    = $themes_body['themes'][0]['id'] ?? null;

    if (!$theme_id) {
        Log::error('update_theme_app_extension - No active theme found');
        return ['status' => 'error', 'message' => 'No active theme found'];
    }

    Log::info('update_theme_app_extension - theme_id: ' . $theme_id);

    // Step 2 — List all assets
    $all_assets  = $fresh_cf->prepare_api_condition(
        ['themes', $theme_id, 'assets'], [], 'GET', '0', $token, $shop
    );
    $assets_body = json_decode(json_encode($all_assets['body']), true);
    $asset_keys  = array_column($assets_body['assets'] ?? [], 'key');

    Log::info('update_theme_app_extension - asset keys count: ' . count($asset_keys));

    // Step 3 — Find settings_data.json
    $settings_asset_key = null;
    foreach ($asset_keys as $key) {
        if ($key === 'config/settings_data.json') {
            $settings_asset_key = $key;
            break;
        }
    }

    if (!$settings_asset_key) {
        Log::error('update_theme_app_extension - settings_data.json not found');
        return ['status' => 'error', 'message' => 'Theme settings file not found'];
    }

    // Step 4 — Fetch settings_data.json
    $asset_response = $fresh_cf->prepare_api_condition(
        ['themes', $theme_id, 'assets'],
        ['asset' => ['key' => $settings_asset_key]],
        'GET', '0', $token, $shop
    );
    $asset_body    = json_decode(json_encode($asset_response['body']), true);
    $settings_json = $asset_body['asset']['value'] ?? null;

    if (!$settings_json) {
        Log::error('update_theme_app_extension - Could not fetch settings_data.json');
        return ['status' => 'error', 'message' => 'Could not fetch theme settings'];
    }

    // Step 5 — Decode JSON
    $settings = json_decode($settings_json, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        Log::error('update_theme_app_extension - Invalid JSON');
        return ['status' => 'error', 'message' => 'Invalid theme settings JSON'];
    }

    // Step 6 — Find and update cookie banner block
    $updated = false;
    if (!empty($settings['current']['blocks'])) {
        foreach ($settings['current']['blocks'] as $block_id => $block) {
            $block_type = $block['type'] ?? '';
            Log::info('update_theme_app_extension - checking block', [
                'block_id'   => $block_id,
                'block_type' => $block_type,
            ]);
            if (stripos($block_type, 'cookie-banner-embed') !== false ||
                stripos($block_type, 'cookie_banner') !== false) {
                $settings['current']['blocks'][$block_id]['settings']['data_key']  = $new_data_key;
                $settings['current']['blocks'][$block_id]['settings']['domain_id'] = (string) $new_domain_id;
                $settings['current']['blocks'][$block_id]['settings']['user_id']   = (string) $new_user_id;
                $settings['current']['blocks'][$block_id]['settings']['cb_js_url'] = $new_cb_js_url;
                $updated = true;
                Log::info('update_theme_app_extension - updated block: ' . $block_id);
            }
        }
    }

    if (!$updated) {
        Log::error('update_theme_app_extension - Cookie banner block not found');
        return ['status' => 'error', 'message' => 'Cookie banner block not found in theme settings'];
    }

    // Step 7 — Re-encode and PUT via direct curl
    $new_settings_json = json_encode($settings, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

    $date  = strtotime('-1 day', strtotime(date('Y-m-d')));
    $month = (int) date('m', $date);
    $year  = date('Y', $date);
    if ($month <= 3)       $api_version = $year . '-01';
    elseif ($month <= 6)   $api_version = $year . '-04';
    elseif ($month <= 9)   $api_version = $year . '-07';
    else                   $api_version = $year . '-10';

    $put_url     = "https://{$shop}/admin/api/{$api_version}/themes/{$theme_id}/assets.json";
    $put_payload = json_encode([
        'asset' => [
            'key'   => $settings_asset_key,
            'value' => $new_settings_json,
        ]
    ]);

    Log::info('update_theme_app_extension - PUT debug', [
        'put_url'       => $put_url,
        'token_preview' => substr($token, 0, 30),
        'api_version'   => $api_version,
    ]);

    $put_ch = curl_init($put_url);
    curl_setopt_array($put_ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST  => 'PUT',
        CURLOPT_POSTFIELDS     => $put_payload,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_HTTPHEADER     => [
            'X-Shopify-Access-Token: ' . $token,
            'Content-Type: application/json',
            'Accept: application/json',
        ],
    ]);

    $put_response = curl_exec($put_ch);
    $http_code    = curl_getinfo($put_ch, CURLINFO_HTTP_CODE);
    $put_err      = curl_error($put_ch);
    curl_close($put_ch);

    if ($put_err) {
        Log::error('update_theme_app_extension - PUT curl error: ' . $put_err);
        return ['status' => 'error', 'message' => 'PUT request failed: ' . $put_err];
    }

    $put_result = json_decode($put_response, true);
    Log::info('update_theme_app_extension - PUT result', [
        'http_code' => $http_code,
        'result'    => $put_result,
    ]);

    if ($http_code !== 200 && $http_code !== 201) {
        Log::error('update_theme_app_extension - PUT failed', [
            'http_code' => $http_code,
            'errors'    => $put_result['errors'] ?? 'Unknown error',
        ]);
        return ['status' => 'error', 'message' => 'Failed to update theme settings: HTTP ' . $http_code];
    }

    // Clean up old script tags
    $this->cleanup_script_tags($shop, $token, $fresh_cf);

    Log::info('update_theme_app_extension - completed successfully');
    return ['status' => 'success', 'message' => 'Theme extension updated successfully'];
}

public function update_theme_app_extension_graphql($shop, $token, $new_data_key, $new_domain_id, $new_user_id, $new_cb_js_url) {

    $fresh_cf = new common_function($shop);
    $store    = $fresh_cf->get_store_detail_obj();
    $token    = $store['token'] ?? $token;

    Log::info('update_theme_graphql - start', [
        'shop'          => $shop,
        'token_preview' => substr($token, 0, 30),
    ]);

    $graphql_url = "https://{$shop}/admin/api/2026-01/graphql.json";

    // Step 1 — Get main theme GID
    $ch = curl_init($graphql_url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST  => 'POST',
        CURLOPT_POSTFIELDS     => json_encode([
            'query' => '{ themes(first: 5, roles: [MAIN]) { nodes { id name role } } }'
        ]),
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_HTTPHEADER     => [
            'X-Shopify-Access-Token: ' . $token,
            'Content-Type: application/json',
        ],
    ]);
    $theme_response = curl_exec($ch);
    curl_close($ch);

    $theme_data = json_decode($theme_response, true);
    $theme_gid  = $theme_data['data']['themes']['nodes'][0]['id'] ?? null;

    Log::info('update_theme_graphql - theme GID: ' . ($theme_gid ?? 'null'));

    if (!$theme_gid) {
        return ['status' => 'error', 'message' => 'No active theme found via GraphQL'];
    }

    // Step 2 — Fetch settings_data.json via GraphQL — corrected query
    // Corrected query — body.content path
        $get_file_query = '
        query getThemeFile($themeId: ID!) {
            theme(id: $themeId) {
                files(filenames: ["config/settings_data.json"], first: 1) {
                    nodes {
                        filename
                        body {
                            ... on OnlineStoreThemeFileBodyText {
                                content
                            }
                        }
                    }
                }
            }
        }';

        $ch = curl_init($graphql_url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST  => 'POST',
            CURLOPT_POSTFIELDS     => json_encode([
                'query'     => $get_file_query,
                'variables' => ['themeId' => $theme_gid],
            ]),
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTPHEADER     => [
                'X-Shopify-Access-Token: ' . $token,
                'Content-Type: application/json',
            ],
        ]);
        $file_response = curl_exec($ch);
        curl_close($ch);

        $file_data = json_decode($file_response, true);

        // Correct path: nodes[0].body.content
        $settings_json = $file_data['data']['theme']['files']['nodes'][0]['body']['content'] ?? null;

        Log::info('update_theme_graphql - file fetch', [
            'has_content' => !empty($settings_json),
            'errors'      => $file_data['errors'] ?? [],
        ]);

        if (!$settings_json) {
            return ['status' => 'error', 'message' => 'Could not fetch settings_data.json via GraphQL'];
        }

    // Step 3 — Update block settings
    $settings_json_clean = preg_replace('#^/\*.*?\*/#s', '', $settings_json);
    $settings_json_clean = trim($settings_json_clean);

    $settings = json_decode($settings_json_clean, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        Log::error('update_theme_graphql - Invalid JSON', [
            'error'     => json_last_error_msg(),
            'first_100' => substr($settings_json_clean, 0, 100),
        ]);
        return ['status' => 'error', 'message' => 'Invalid JSON in settings_data.json: ' . json_last_error_msg()];
    }

    $updated = false;
    if (!empty($settings['current']['blocks'])) {
        foreach ($settings['current']['blocks'] as $block_id => $block) {
            $block_type = $block['type'] ?? '';
            if (stripos($block_type, 'cookie-banner-embed') !== false ||
                stripos($block_type, 'cookie_banner') !== false) {

                $settings['current']['blocks'][$block_id]['settings']['data_key']  = $new_data_key;
                $settings['current']['blocks'][$block_id]['settings']['domain_id'] = (string) $new_domain_id;
                $settings['current']['blocks'][$block_id]['settings']['user_id']   = (string) $new_user_id;
                $settings['current']['blocks'][$block_id]['settings']['cb_js_url'] = $new_cb_js_url;
                $updated = true;

                Log::info('update_theme_graphql - updated block: ' . $block_id);
            }
        }
    }

    if (!$updated) {
        return ['status' => 'error', 'message' => 'Cookie banner block not found in theme settings'];
    }

    $new_settings_json = json_encode($settings, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

    // Step 4 — Upsert file via GraphQL mutation
    $mutation = '
    mutation themeFilesUpsert($files: [OnlineStoreThemeFilesUpsertFileInput!]!, $themeId: ID!) {
        themeFilesUpsert(files: $files, themeId: $themeId) {
            upsertedThemeFiles {
                filename
            }
            userErrors {
                field
                message
            }
        }
    }';

    $ch = curl_init($graphql_url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST  => 'POST',
        CURLOPT_POSTFIELDS     => json_encode([
            'query'     => $mutation,
            'variables' => [
                'themeId' => $theme_gid,
                'files'   => [
                    [
                        'filename' => 'config/settings_data.json',
                        'body'     => [
                            'type'    => 'TEXT',
                            'value' => $new_settings_json,
                        ],
                    ]
                ],
            ],
        ]),
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_HTTPHEADER     => [
            'X-Shopify-Access-Token: ' . $token,
            'Content-Type: application/json',
        ],
    ]);

    $mutation_response = curl_exec($ch);
    $mutation_http     = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $mutation_result = json_decode($mutation_response, true);

    Log::info('update_theme_graphql - mutation result', [
        'http_code' => $mutation_http,
        'result'    => $mutation_result,
    ]);

    $user_errors = $mutation_result['data']['themeFilesUpsert']['userErrors'] ?? [];
    if (!empty($user_errors)) {
        Log::error('update_theme_graphql - userErrors', ['errors' => $user_errors]);
        return ['status' => 'error', 'message' => 'GraphQL mutation failed: ' . json_encode($user_errors)];
    }

    if (empty($mutation_result['data']['themeFilesUpsert']['upsertedThemeFiles'])) {
        Log::error('update_theme_graphql - no upserted files', ['result' => $mutation_result]);
        return ['status' => 'error', 'message' => 'GraphQL mutation returned no updated files'];
    }

    Log::info('update_theme_graphql - completed successfully');
    return ['status' => 'success', 'message' => 'Theme extension updated successfully via GraphQL'];
}

public function update_shop_metafields($shop, $token, $data_key, $domain_id, $user_id, $cb_js_url) {

    $graphql_url = "https://{$shop}/admin/api/2026-01/graphql.json";

    // Step 1 — Get shop GID
    $ch = curl_init($graphql_url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST  => 'POST',
        CURLOPT_POSTFIELDS     => json_encode([
            'query' => '{ shop { id } }'
        ]),
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_HTTPHEADER     => [
            'X-Shopify-Access-Token: ' . $token,
            'Content-Type: application/json',
        ],
    ]);
    $shop_response = curl_exec($ch);
    curl_close($ch);

    $shop_data = json_decode($shop_response, true);
    $shop_gid  = $shop_data['data']['shop']['id'] ?? null;

    if (!$shop_gid) {
        Log::error('update_shop_metafields - Could not get shop GID');
        return ['status' => 'error', 'message' => 'Could not get shop GID'];
    }

    Log::info('update_shop_metafields - shop GID: ' . $shop_gid);

    // Step 2 — Set all 4 metafields
    $mutation = '
    mutation metafieldsSet($metafields: [MetafieldsSetInput!]!) {
        metafieldsSet(metafields: $metafields) {
            metafields {
                key
                namespace
                value
                ownerType
            }
            userErrors {
                field
                message
                code
            }
        }
    }';

    $ch = curl_init($graphql_url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST  => 'POST',
        CURLOPT_POSTFIELDS     => json_encode([
            'query'     => $mutation,
            'variables' => [
                'metafields' => [
                    [
                        'namespace' => 'seers_cmp',
                        'key'       => 'data_key',
                        'value'     => $data_key,
                        'type'      => 'single_line_text_field',
                        'ownerId'   => $shop_gid,
                    ],
                    [
                        'namespace' => 'seers_cmp',
                        'key'       => 'domain_id',
                        'value'     => (string) $domain_id,
                        'type'      => 'single_line_text_field',
                        'ownerId'   => $shop_gid,
                    ],
                    [
                        'namespace' => 'seers_cmp',
                        'key'       => 'user_id',
                        'value'     => (string) $user_id,
                        'type'      => 'single_line_text_field',
                        'ownerId'   => $shop_gid,
                    ],
                    [
                        'namespace' => 'seers_cmp',
                        'key'       => 'cb_js_url',
                        'value'     => $cb_js_url,
                        'type'      => 'single_line_text_field',
                        'ownerId'   => $shop_gid,
                    ],
                ],
            ],
        ]),
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_HTTPHEADER     => [
            'X-Shopify-Access-Token: ' . $token,
            'Content-Type: application/json',
        ],
    ]);

    $response  = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $result = json_decode($response, true);

    Log::info('update_shop_metafields - result', [
        'http_code'   => $http_code,
        'metafields'  => $result['data']['metafieldsSet']['metafields'] ?? [],
        'user_errors' => $result['data']['metafieldsSet']['userErrors'] ?? [],
    ]);

    $user_errors = $result['data']['metafieldsSet']['userErrors'] ?? [];
    if (!empty($user_errors)) {
        Log::error('update_shop_metafields - userErrors', ['errors' => $user_errors]);
        return ['status' => 'error', 'message' => 'Metafields update failed: ' . json_encode($user_errors)];
    }

    if (empty($result['data']['metafieldsSet']['metafields'])) {
        Log::error('update_shop_metafields - no metafields returned');
        return ['status' => 'error', 'message' => 'No metafields updated'];
    }

    Log::info('update_shop_metafields - completed successfully');
    return ['status' => 'success', 'message' => 'Shop metafields updated successfully'];
}

private function cleanup_script_tags($shop, $token, $fresh_cf = null) {
    try {
        // Use fresh_cf if provided to ensure correct token
        $cf = $fresh_cf ?? new common_function($shop);

        $allscriptags = $cf->prepare_api_condition(
            ['script_tags'], [], 'GET', '0', $token, $shop
        );

        $tags_body = json_decode(json_encode($allscriptags['body']), true);

        if (!empty($tags_body['script_tags'])) {
            foreach ($tags_body['script_tags'] as $script) {
                if (stripos($script['src'], 'cdn.seersco.com/banners/') !== false) {
                    $cf->prepare_api_condition(
                        ['script_tags', $script['id']],
                        [], 'DELETE', '0', $token, $shop
                    );
                    Log::info('cleanup_script_tags - deleted: ' . $script['src']);
                }
            }
        }
    } catch (\Exception $e) {
        Log::error('cleanup_script_tags failed: ' . $e->getMessage());
    }
}


    public function get_main_theme_id($shop, $token) {
        $themes = $this->prepare_api_condition(
            ['themes'], ['role' => 'main'], 'GET', '0', $token, $shop
        );
        return $themes['body']['themes'][0]['id'] ?? null;
    }

    public function shopify_redirect($url, $type = 'REMOTE') {
        $host = isset($_REQUEST['host']) ? $_REQUEST['host'] : '';

        if (!empty($host)) {
            echo '<!DOCTYPE html>
            <html>
            <head>
                <script src="https://unpkg.com/@shopify/app-bridge@3"></script>
                <script>
                    var AppBridge = window["app-bridge"];
                    var app = AppBridge.createApp({
                        apiKey: "' . config('app.shopify_apikey') . '",
                        host: "' . $host . '",
                        forceRedirect: true
                    });
                    var Redirect = AppBridge.actions.Redirect;
                    var redirect = Redirect.create(app);
                    redirect.dispatch(Redirect.Action.REMOTE, "' . $url . '");
                </script>
            </head>
            <body></body>
            </html>';
            exit;
        } else {
            header('Location: ' . $url);
            exit;
        }
    }

    // public function snippest_insert_v2($shop, $token, $domain, $email) {

    //     $selected_field = 'data_key';
    //     $where = ['shop' => $shop, 'status' => '1'];
    //     $store_row_rs = $this->select_row(config('app.table_user_stores'), $selected_field, $where);

    //     $store_row = (count($store_row_rs) > 0 && !empty($store_row_rs[0]->email))
    //         ? (array) $store_row_rs[0]
    //         : ((!$store_row_rs->isEmpty()) ? (array) $store_row_rs[0] : []);

    //     $datakey = $store_row['data_key'] ?? '';

    //     $response = $this->get_data_key($domain, $email);
    //     $datakey   = $response['key'] ?? $datakey;
    //     $domain_id = $response['domain_id'] ?? '';
    //     $user_id   = $response['user_id'] ?? '';
    //     $scriptbaseurl = $response['cdnbaseurl'] ?? "https://cdn.seersco.com/";

    //     $fields = [
    //         'data_key' => $datakey,
    //         'domain_id' => $domain_id,
    //         'user_id' => $user_id
    //     ];
    //     $this->update(config('app.table_user_stores'), $fields, ['shop' => $shop]);

    //     if(!empty($user_id) && !empty($domain_id)) {
    //         $cb_js_url = $scriptbaseurl . 'banners/' . $user_id . '/' . $domain_id . '/cb.js?param=' . $datakey . '&name=CookieXray';
    //     } else {
    //        $cb_js_url = 'https://cdn.seersco.com/banners/default/default/cb.js?param='
    //        . $datakey . '&name=CookieXray';
    //     }

    //     $allscriptags = $this->prepare_api_condition(['script_tags'], [], 'GET', '0', $token, $shop);

    //     $script_exists = false;

    //     if(!empty($allscriptags['body']['script_tags'])) {
    //         foreach ($allscriptags['body']['script_tags'] as $thescript) {

    //             if(strpos($thescript['src'], 'cdn.seersco.com/banners/') !== false && strpos($thescript['src'], '/cb.js') !== false) {
    //                 preg_match('#banners/([0-9]+)/([0-9]+)/cb\.js\?param=([^&]+)#', $thescript['src'], $m);

    //                 if(!empty($m) && ($m[1] != $user_id || $m[2] != $domain_id || urldecode($m[3]) != $datakey)) {
    //                     $this->prepare_api_condition(['script_tags', $thescript['id']], [], 'DELETE', '0', $token, $shop);
    //                     continue;
    //                 }

    //                 if(!empty($m) && $m[1] == $user_id && $m[2] == $domain_id && urldecode($m[3]) == $datakey) {
    //                     $script_exists = true;
    //                 }
    //             }
    //         }
    //     }

    //     if(!$script_exists) {
    //         $this->prepare_api_condition(['script_tags'], [
    //             'script_tag' => [
    //                 'event' => 'domcontentloaded',
    //                 'src' => $cb_js_url,
    //                 'display_scope' => 'online_store',
    //                 'attributes' => ['data-shopify-cmp' => '']
    //             ]
    //         ], 'POST', '0', $token, $shop);
    //     }

    //     return $allscriptags;
    // }


    public function insertConsentTrackingScript($shop, $token)
    {
        $consentScriptUrl = "https://cdn.shopify.com/shopifycloud/consent-tracking-api/v0.1/consent-tracking-api.js";

        $existingTags = $this->prepare_api_condition(['script_tags'], [], 'GET', 0, $token, $shop);
        $scriptExists = false;

        if (!empty($existingTags['body']['script_tags'])) {
            foreach ($existingTags['body']['script_tags'] as $tag) {
                if (!empty($tag['src']) && strcasecmp($tag['src'], $consentScriptUrl) === 0) {
                    $scriptExists = true;
                    break;
                }
            }
        }

        if (!$scriptExists) {
            $payload = [
                'script_tag' => [
                    'event' => 'onload',
                    'src' => $consentScriptUrl,
                    'display_scope' => 'online_store',
                ],
            ];

            $response = $this->prepare_api_condition(['script_tags'], $payload, 'POST', 0, $token, $shop);

            return $response;
        }

        return "Already exists";
    }


    public function plugin_active_inactive($shopdetail, $isative = 0){
        $postData = array(
            'domain' => $shopdetail['shop'],
            'isactive' => $isative,
            'secret' => $this->apisecrekkey,
            'platform' => 'shopify',
            'pluginname' => $shopdetail['name']
        );
        $request_headers = array(
            'Content-Type' => 'application/json',
            'Referer' => $shopdetail['shop'],
        );
        //$url = "https://seersco.backend/api/plugin-domain";
        $url = $this->apibaseurl . "plugin-domain";
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            // CURLOPT_TIMEOUT => 0,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_TIMEOUT => 8,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_HTTPHEADER => $request_headers,
            CURLOPT_POSTFIELDS => $postData
        ));

        $response = curl_exec($curl);
        if (curl_errno($curl) || empty($response)) {
            \Log::error('plugin_active_inactive failed: ' . curl_error($curl));
            curl_close($curl);
            return [];
        }
        $error_number = curl_errno($curl);
        $error_message = curl_error($curl);
        curl_close($curl);

        $response =json_decode($response, TRUE);

        return $response;
    }

    public function mres($value) {
        $search = array("\\",  "\x00", "\n",  "\r",  "'",  '"', "\x1a");
        $replace = array("\\\\","\\0","\\n", "\\r", "\'", '\"', "\\Z");

        return str_replace($search, $replace, $value);
    }

}
