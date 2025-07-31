<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\INC\common_function;
use App\INC\user_functions;

class UserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function userdahsboard()
    {
        $siteurl = config("app.url");
        $devmode = 'dev';
        $current_user = "";
        if (isset($_REQUEST['shop']) && $_REQUEST['shop'] != '') {
            $shop = $_REQUEST['shop'];
            $uf_obj = new User_functions($shop);
            $current_user = $uf_obj->get_store_detail_obj();
            if (empty($current_user)) {
                header('Location:' . $siteurl . "404");
                exit;
            }

            $devmode = 'dev';
            
            if (stripos($siteurl, 'gdpr-cookie-consent-banner-cookie-notice-seers') === false) {
                // live mode
                $devmode = 'live';
            }

        } else {
            header('Location:' . $siteurl . "404");
            exit;
        }

        return view('user', ['sitename' => config("app.site_name"), 'sitemail' => config("app.site_email"), 'shopvar' => $shop, 'MODE' => $devmode, "current_user" => $current_user, "SHOPIFY_API_KEY" => config("app.shopify_apikey")]);
    }
}
