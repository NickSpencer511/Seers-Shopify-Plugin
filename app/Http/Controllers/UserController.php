<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\INC\common_function;
use App\INC\user_functions;

class UserController extends Controller
{
    public function __construct() {
    }

    public function userdahsboard()
    {
        $siteurl = config("app.url");
        $host = !empty($_REQUEST['host']) ? $_REQUEST['host'] : '';
        $devmode = 'dev';
        $current_user = "";

        if (isset($_REQUEST['shop']) && $_REQUEST['shop'] != '') {
            $shop = $_REQUEST['shop'];
            $uf_obj = new user_functions($shop);
            $current_user = $uf_obj->get_store_detail_obj();

            if (empty($current_user)) {
                $cf_obj = new common_function();
                $cf_obj->shopify_redirect($siteurl . '/404', 'REMOTE');
                exit;
            }

            if (
                stripos($siteurl, 'ngrok') !== false ||
                stripos($siteurl, 'localhost') !== false ||
                stripos($siteurl, '127.0.0.1') !== false
            ) {
                $devmode = 'dev';
            } else {
                $devmode = 'live';
            }

            // Build editor_url for Theme Editor deep link
            $editor_url = null;
            if (!empty($current_user['token'])) {
                $cf_obj = new common_function();
                $theme_id = $cf_obj->get_main_theme_id($shop, $current_user['token']);
                if ($theme_id) {
                    $client_id  = config('app.shopify_apikey');
                    $editor_url = "https://{$shop}/admin/themes/{$theme_id}/editor"
                        . "?context=apps"
                        . "&appEmbed={$client_id}%2Fcookie_banner";
                }
            }

        } else {
            $cf_obj = new common_function();
            $cf_obj->shopify_redirect($siteurl . '/404', 'REMOTE');
            exit;
        }

        return view('user', [
            'sitename'        => config("app.site_name"),
            'sitemail'        => config("app.site_email"),
            'shopvar'         => $shop,
            'MODE'            => $devmode,
            'current_user'    => $current_user,
            'SHOPIFY_API_KEY' => config("app.shopify_apikey"),
            'editor_url'      => $editor_url,
        ]);
    }
}
