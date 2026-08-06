<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCbJsUrlToUserStoresTable extends Migration
{
    public function up()
    {
        Schema::table('user_stores', function (Blueprint $table) {
            $table->string('cb_js_url', 500)->nullable()->after('data_key');
        });
    }

    public function down()
    {
        Schema::table('user_stores', function (Blueprint $table) {
            $table->dropColumn('cb_js_url');
        });
    }
}
