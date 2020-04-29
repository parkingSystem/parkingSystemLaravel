<?php

use Illuminate\Http\Request;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
//User routes
Route::post('createUser',"UserController@createUser");
Route::post('login',"UserController@login");
Route::get('getUserInfo',"UserController@getUserInfo");
Route::get('getAllOrders',"UserController@getAllOrders");
Route::patch('updateUser',"UserController@updateUser");
Route::patch('updateBalance',"UserController@updateBalance");
//park routes
Route::get('getAllParks',"ParkController@getAllParks");
Route::get('getPark',"ParkController@getPark");

Route::post('saveIncomingImage',"UserImageController@saveIncomingImage");
Route::post('saveOutgoingImage',"UserImageController@saveOutgoingImage");
//order routes
Route::post('createOrder',"OrderController@createOrder");
Route::post('validateIn',"OrderController@validateIn");
Route::post('validateOut',"OrderController@validateOut");
