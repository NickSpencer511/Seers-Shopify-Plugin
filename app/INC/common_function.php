<?php
namespace App\INC;

use App\INC\DB_Class;
use Carbon\Carbon;
use Str;
use DateTime;
use DB;

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
    public $apisecrekkey = '$2y$10$9ygTfodVBVM0XVCdyzEUK.0FIuLnJT0D42sIE6dIu9r/KY3XaXXyS';
    protected $apibaseurl = "https://cmp.seersco.com/api/v2/";
    protected $last_query = '';

    public function __construct($shop = '') {
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

        switch ($month) {
            case $month <= 3:
                $shopify_api_version = $year . '-01';
                break;
            case $month <= 6:
                $shopify_api_version = $year . '-04';
                break;
            case $month <= 9:
                $shopify_api_version = $year . '-07';
                break;
            case $month <= 12:
                $shopify_api_version = $year . '-10';
                break;
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

    public function snippest_insert($shop, $token, $domain, $email) {
         
        $selected_field = 'data_key';
        $where = array('shop' => $shop, 'status' => '1');
        $store_row_rs = $this->select_row(config('app.table_user_stores'), $selected_field, $where);

        $store_row = (count($store_row_rs) > 0 && !empty($store_row_rs[0]->email)) ? (array) $store_row_rs[0] : ((!$store_row_rs->isEmpty()) ? (array) $store_row_rs[0] : [] ) ;

        $old_script = '';
        $datakey = '';
        //live cdn script baseurl
        $scriptbaseurl = "https://cdn.seersco.com/";

        if(!empty($store_row)){
            $datakey = $store_row['data_key'];
        }
        
        $response = $this->get_data_key($domain, $email);


        if(!empty($response['key']))
            $datakey = $response['key'];
        else
            $datakey = "";

        if(!empty($response['access_token']))
            $access_token = $response['access_token'];
        else
            $access_token = "";

        if(!empty($response['domain_id']))
            $domain_id = $response['domain_id'];
        else
            $domain_id = "";

        if(!empty($response['user_id']))
            $user_id = $response['user_id'];
        else
            $user_id = "";

        if(!empty($response['cdnbaseurl']))
            $scriptbaseurl = $response['cdnbaseurl'];
        
        $fields['data_key'] = $datakey;
        $fields['access_token'] = $access_token;
        $fields['domain_id'] = $domain_id;
        $fields['user_id'] = $user_id;

        $where = array('shop' => $shop);
        $last_id = $this->update(config('app.table_user_stores'), $fields, $where);
        
        //$arrsrc = ['https://cmp.seersco.com/script/cb.js', 'https://seers-application-assets.s3.amazonaws.com/scripts/cbattributes.js?key=' . $datakey . '&name=CookieXray'];

        $cbattrjspath = 'https://seers-application-assets.s3.amazonaws.com/scripts/cbattributes.js';
        
        if ($_SERVER['SERVER_NAME'] == 'localhost')
            $cbattrjspath = 'https://localhost/private-apps/script/cbattributes-localhost.js';
            
        if(!empty($response['user_id']) && !empty($response['domain_id'])) {
            //$arrsrc = [ $scriptbaseurl . 'banners/' . $response['user_id'] . '/' . $response['domain_id'] . '/cb.js', $cbattrjspath . '?key=' . $datakey . '&name=CookieXray'];
            $arrsrc = [ $scriptbaseurl . 'banners/' . $response['user_id'] . '/' . $response['domain_id'] . '/cb.js' . '?param=' . $datakey . '&name=CookieXray'];
        } else {
            //$arrsrc = [ 'https://seerscophp8.backend/script/cb.js', $cbattrjspath . '?key=' . $datakey . '&name=CookieXray'];
            $arrsrc = [ 'https://seerscophp8.backend/script/cb.js' . '?param=' . $datakey . '&name=CookieXray'];
        }
            

        $arrscriptexist = [false, false];
        
        
        //get all avialable tags
        $allscriptags = $this->prepare_api_condition(array('script_tags'), array(), 'GET', '0', $token, $shop);
        
        //print_r($allscriptags);
        
        if(!empty($allscriptags['body']) && !empty($allscriptags['body']['script_tags'])) {
            
            foreach ($allscriptags['body']['script_tags'] as $thescript) {
                
                if (strcasecmp($thescript['src'], $arrsrc[0]) === 0) {
                    $arrscriptexist[0] = true;
                } else if (strcasecmp($thescript['src'], $arrsrc[1]) === 0) {
                    $arrscriptexist[1] = true;
                } else if (stripos($thescript['src'], $cbattrjspath) !== false && strcasecmp($thescript['src'], $arrsrc[1]) !== 0) {
                    $arrscriptexist[1] = false;
                    //remove the script
                    $scriptdel = $this->prepare_api_condition(array('script_tags', $thescript['id']), array(), 'DELETE', '0', $token, $shop);
                }
            }
            
            
        }
        
        foreach ($arrsrc as $sitind => $sitesrc) {
            
            if (!$arrscriptexist[$sitind]) {
                
                //add this src in scripts
                $scriptinsert = $this->prepare_api_condition(array('script_tags'), array('script_tag' => array( "event"=>"onload", "src"=>$sitesrc, "display_scope" => "online_store","attributes" => array("data-shopify-cmp" => ""))), 'POST', '0', $token, $shop);
                
            }
            
        }


        // $responseUser = $this->get_user_data($domain, $email);

        // echo "<pre>";
        // print_r($responseUser);
        // echo "</pre>";

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
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_HTTPHEADER => $request_headers,
            CURLOPT_POSTFIELDS => $postData
        ));

        $response = curl_exec($curl);
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
