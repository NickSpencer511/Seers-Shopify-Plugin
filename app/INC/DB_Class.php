<?php
namespace App\INC;
use Carbon\Carbon;
use Str;
use DateTime;

/*
 * Database connection
 *  */
class DB_Class {

    function __construct() {
        if (!isset($GLOBALS['conn']) && empty($GLOBALS['conn'])) {
            $objdbconn = mysqli_connect(config('database.connections.mysql.host'), config('database.connections.mysql.username'), config('database.connections.mysql.password'), config('database.connections.mysql.database'));
            if (mysqli_connect_errno()) {
                echo "Failed : connect to MySQL: " . mysqli_connect_error();
                die;
            }
            $GLOBALS['conn'] = $objdbconn;
            mysqli_set_charset($objdbconn, "utf8mb4");
            return $objdbconn;
        }
    }

}