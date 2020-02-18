<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\AdminLv2UserGroupIdModel;
use App\Models\TcLenhConfigModel;
use App\Models\TcLenhModel;

class QuanLyLenhController extends Controller
{

    /* 
    *           LAY DANH SACH TAI XE
    */
    public function getLaiXeList(AdminLv2UserGroupIdModel $user_group)
    {
        $data = $user_group->join('admin_lv2_user',function($join){
            $join->on('admg_admin_id','=','adm_id');
        })->get();

        return response()->json($data, 200);
    }
    /* 
    *           LAY TRANG THAI LENH CONFIG
    */
    public function getTrangThaiLenhConfig(TcLenhConfigModel $tc_lenh)
    {
        $data = $tc_lenh->all();

        return response()->json($data,200);
    }

    /* 
    *           LAY MA LENH
    */
    public function getMaLenh(TcLenhModel $lenh)
    {
        $data =  $lenh->all();

        return response()->json($data, 200);
    }

    /* 
    *           SEARCH MA LENH
    */
    public function searchMaLenh(TcLenhModel $lenh, Request $request)
    {
        $request = $request->all();
        
        $data = $lenh
        ->join('admin_lv2_user','lai_xe_id','=','adm_id')
        ->join('xe','tc_lenh.xe_id','=','xe.xe_id')
        ->join('ben_xe','diem_giao_khach','=','bex_id')
        ->where([
            ['kieu_lenh','=',$request['kieuLenh']],
            ['tc_lenh_id','=',$request['lenhId']],
            ['lai_xe_id','=',$request['taiXeId']],
            ['trang_thai','=',$request['trangThai']]
            
        ])->whereDate('created_date',$request['ngayTao'])
        ->get();

        return response()->json($data, 200);
    }
    
    public function getThongTinMaLenh(TcLenhModel $lenh, $malenh = null){
        $data = $lenh
        ->join('admin_lv2_user','lai_xe_id','=','adm_id')
        ->where('tc_lenh_id','=',$malenh)->get();

        return response()->json($data, 200);
    }
    /* 
    *           LAY DANH SACH KHACH DANG DON THEO 1 LENH
    */
    public function getListKhachDangDon(TcLenhModel $lenh, $malenh = null){
        $data = $lenh
        ->join('admin_lv2_user','lai_xe_id','=','adm_id')
        ->join('tc_ve','tc_lenh.tc_lenh_id','=','tc_ve.tc_lenh_id')
        ->join('dieu_do_temp','tc_ve.did_id','=','dieu_do_temp.did_id')
        ->join('not_tuyen','did_not_id','=','not_id')
        ->join('tuyen','not_tuy_id','=','tuy_id')
        ->join('xe','tc_lenh.xe_id','=','xe.xe_id')
        ->where('tc_lenh.tc_lenh_id','=',$malenh)->get();

        return response()->json($data, 200);
    }

}
