<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\WebhooksController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/showoutput', function (Request $request) {
    return config('database.connections.mysql');
});

//Shopify hooks endpoints...
Route::post('/shop-info-remove', [WebhooksController::class, 'ShopinfoRemove']);
Route::post('/customer-request', [WebhooksController::class, 'CustomerRequest']);
Route::post('/customer-data', [WebhooksController::class, 'CustomerData']);
Route::post('/app-uninstalled', [WebhooksController::class, 'AppUninstalled']);
Route::post('/shop-update', [WebhooksController::class, 'ShopUpdate']);
Route::post('/ajax_actions', [WebhooksController::class, 'AjaxAction']);
