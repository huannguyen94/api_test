<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\TccaModel;
use DB;

class QuanlycaController extends Controller
{
    // ---------tìm kiếm ca--------
    public function timKiemCa(TccaModel $data_catruc, Request $request)
    {
        $res = $request->all();
        $data = $data_catruc->where([
            ['ghi_chu','like','%'.$res['ghiChu'].'%'],
            ['gio_bat_dau','=',$res['gioBatDau']],
            ['gio_ket_thuc','=',$res['gioKetThuc']],
            ['ma_ca','like','%'.$res['maCa'].'%'],
            ['ten_ca','like','%'.$res['tenCa'].'%'],
            ['trang_thai','=',$res['trangThai']]
        ])->orderBy($res['sortBy'], $res['sortType'])->get();

        return response()->json($data, 200);
    }
    
    // ---------tạo mới ca--------
    public function taoMoica(TccaModel $data_catruc, Request $request)
    {
        $data = $request->all();
        $res = TccaModel::create([
            'ghi_chu'=>$data['ghiChu'],
            'gio_bat_dau'=>$data['gioBatDau'],
            'gio_ket_thuc'=>$data['gioKetThuc'],
            'ma_ca'=>$data['maCa'],
            'ten_ca'=>$data['tenCa'],
            'trang_thai'=>$data['trangThai'],
        ]);
        
        return response()->json($data, 201);
    }

    // ---------cập nhật ca--------
    public function capNhatCa(Request $request)
    {
        $data = $request->all();
        DB::table('tc_ca')
        ->where('tc_ca_id', '=', $request['tcCaId'])
        ->update([
            'ghi_chu'=>$data['ghiChu'],
            'gio_bat_dau'=>$data['gioBatDau'],
            'gio_ket_thuc'=>$data['gioKetThuc'],
            'ma_ca'=>$data['maCa'],
            'ten_ca'=>$data['tenCa'],
            'trang_thai'=>$data['trangThai'],
        ]);
        $res =TccaModel::where('tc_ca_id', '=', $request['tcCaId'])->get();
        return response()->json($res, 200);
    }
}
