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
	Route::group(['prefix' => 'quanlystaff'], function () {
		Route::get('staff','UserController@getListingStaff');
		Route::put('put_staff','UserController@putStaff');
	});
	
	Route::get('branch','UserController@getListingBranch');
	Route::get('xe_tc','XeTCController@getListingXeTC');
	Route::put('put_xe_tc','XeTCController@putXeTC');
	
});



