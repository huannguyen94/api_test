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


	/****************GET DANH SACH TAI XE ************************/ 
	Route::group(['prefix' => 'lichtruc'],function(){
		Route::get('danhsachtaixe','QuanLyLenhController@getLaiXeList');
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
});






Route::get('/',function(){

});