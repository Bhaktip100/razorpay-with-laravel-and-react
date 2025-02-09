<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RazorpayController;

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
// header('Access-Control-Allow-Origin: *');  
// header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/razorpay/create-order', 'RazorpayController@createOrder');
Route::post('/razorpay/fetch-payment-detail', 'RazorpayController@fetchPaymentDetails');
Route::post('/webhook/razorpay', 'RazorpayController@handleWebhook');
