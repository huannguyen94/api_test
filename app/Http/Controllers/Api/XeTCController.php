<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use DB;

class XeTCController extends Controller
{
    public function getListingXeTC (Request $request)
    {
    	$limit = (int) $request->get('limit',50);
    	$page = (int) $request->get('page',50);

    	$data_xe_tc = DB::table('xe')->where('xe_trung_tam',4)->where('xe_status',1)
                    ->where([['xe_bien_kiem_soat','like','%'.$request['xe_bien_kiem_soat'].'%'],])
                    ->paginate($limit);

    	foreach($data_xe_tc as $key => $value){
    		$response[] = [
				"id"                => $value->xe_id,
				"bien_kiem_soat"    => $value->xe_bien_kiem_soat,
				// "bien_kiem_soat_so" => $value->xe_bien_kiem_soat_so,
				// "sdt"               => $value->xe_so_dien_thoai,
				// "so_cho"            => $value->xe_so_cho,
				// "loai_xe"           => $value->xe_loai,
				// "loai_so_do_giuong" => $value->xe_loai_so_do_giuong,
				// "nhom_xe"           => $value->xe_nhom_id,
				// "hang_xe"           => $value->xe_hang,
				// "vung_hoat_dong"    => $value->xe_vung_hoat_dong_id,
    		];
    	}
        return response()->json($response);
    }

    public function putXeTC (Request $request)
    {
    	$id = $request->adm_id;
    	$update = [
			"xe_id"          => $request->xe_id,
			"vung_hoat_dong" => $request->xe_vung_hoat_dong_id,
		];

    	$data= DB::table('xe')->where('xe_id',$id)->update($update);
    	
    	return $data;
    }
}
