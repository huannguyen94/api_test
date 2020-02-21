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

require (__DIR__ . '/huan.php');
require (__DIR__ . '/hieu.php');
require (__DIR__ . '/long.php');


Route::group(['namespace'=>'Api'],function(){

	Route::group(['prefix' => 'auth'], function () {
		Route::post('login','AuthController@login');
		Route::post('logout','AuthController@logout');
	});

	
	/****************GET TRANG THAI LENH CONFIG ************************/ 
	Route::get('lenhconfig','QuanLyLenhController@getTrangThaiLenhConfig');
	
	Route::group(['prefix' => 'dieudo'],function(){
		Route::group(['prefix' => 'lenh'],function(){
			/****************GET MA LENH  ************************/ 
			Route::get('malenh','QuanLyLenhController@getMaLenh');

			/****************SEARCH MA LENH  ************************/ 
			Route::post('timkiem','QuanLyLenhController@searchMaLenh');

			/****************GET THONG TIN CHI TIET MA LENH  ************************/ 
			Route::get('chitiet/{malenh}','QuanLyLenhController@getThongTinMaLenh');
		});

			/****************LAY DANH SACH KHACH DANG DON ************************/ 
			Route::group(['prefix' =>'tcd'],function(){
				Route::get('dskhachdangdon/{malenh}','QuanLyLenhController@getListKhachDangDon');
			});
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



