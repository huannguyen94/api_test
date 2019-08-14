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




Route::group(['namespace'=>'Api'],function(){

	
    Route::get('test/{id}','WebHookErpController@webHookErp');


	Route::group(['middleware' => ['apiToken'] ], function () {
		Route::get('/rule','ApiTestController@testApi')->name('aaaaa');
	});
});

Route::middleware('auth:api')->get('/user', function (Request $request) {

    return $request->user();
});
