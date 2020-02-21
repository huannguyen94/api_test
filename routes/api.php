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

	Route::group(['prefix' => 'auth'], function () {
		Route::post('login','AuthController@login');
		Route::post('logout','AuthController@logout');
	});

	Route::group(['prefix' => 'quanlyca'], function () {
		Route::get('test','ApiTestController@testApi');
	});

    Route::group(['prefix' => 'quanlyca'], function () {
        Route::get('test', 'ApiTestController@testApi');
    });
    Route::group(['prefix'=>'catruc'], function () {
		// ------------- tìm kiếm ca------------
		Route::post('search', 'QuanlycaController@timKiemCa');
		// ------------- cập nhật ca------------
		Route::post('update', 'QuanlycaController@capNhatCa');
		// ------------- tạo mới ca------------
        Route::post('createnew', 'QuanlycaController@taoMoiCa');
    });


});

	


