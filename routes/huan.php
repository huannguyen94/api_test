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
	Route::group(['prefix' => 'quanlyca'], function () {
		Route::get('test3','ApiTestController@testApi');
	});
	Route::group(['prefix' => 'quanlyuser'], function () {
		Route::get('user','UserController@getListingUser');
	});
	
});



