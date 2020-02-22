<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use DB;

class UserController extends Controller
{
    public function getListingStaff (Request $request)
    {
        $limit = (int) $request->get('limit',50);
        $page = (int) $request->get('page',0);

		$data_user  = DB::table('admin_lv2_user')->where('adm_group_id',2)->orwhere('adm_group_id',4)
					->where([
						['adm_name','like','%'.$request['name'].'%'],
						['adm_ma','like','%'.$request['ma']],
						['adm_phone','like','%'.$request['phone']],
						['adm_noi_lam_viec','like','%'.$request['noi_lam_viec'].'%'],
    				])->paginate($limit);

		$data_chi_nhanh = DB::table('chi_nhanh')->get();

		$chi_nhanh = [];
		foreach ($data_chi_nhanh as $key => $value1) {
			$chi_nhanh[$value1->cn_id] = $value1->cn_name;
		}

    	foreach ($data_user as $key => $value) {
	    	$response[] = [	
				"id"           => $value->adm_id,
				"name"         => $value->adm_name,
				"ma"           => $value->adm_ma,
				"phone"        => $value->adm_phone,
				"id_nlv"       => $value->adm_noi_lam_viec,
				"noi_lam_viec" => isset($chi_nhanh[$value->adm_noi_lam_viec]) ? $chi_nhanh[$value->adm_noi_lam_viec] : "",
				"vp_kt"        => $value->adm_vp_kiem_nghiem
	    	];
    	}
        return response()->json($response);
    }

    public function putStaff(Request $request)
    {
    	$id = $request->adm_id;
    	$update = [
			"adm_id"             => $request->adm_id,
			"adm_noi_lam_viec"   => $request->adm_noi_lam_viec,
			"adm_vp_kiem_nghiem" => $request->adm_vp_kiem_nghiem,
		];

    	$data= DB::table('admin_lv2_user')->where('adm_id',$id)->update($update);
    	
    	return $data;
    }


    public function getListingBranch (Request $request)
    {
    	$limit = (int) $request->get('limit',50);
        $page = (int) $request->get('page',0);

		$data_branch = DB::table('chi_nhanh')->paginate($limit);

		$chi_nhanh_cha = [];
		foreach ($data_branch as $key => $value1) {
			$chi_nhanh_cha[$value1->cn_id] = $value1->cn_name;
		}

    	foreach ($data_branch as $key => $value) {
	    	$response[] = [	
				"id"      => $value->cn_id,
				"cn_cha"  => isset($chi_nhanh_cha[$value->cn_parent_id]) ? $chi_nhanh_cha[$value->cn_parent_id] : "",
				"name"    => $value->cn_name,
				"ma"      => $value->cn_code,
				"dia_chi" => $value->cn_address,
	    		
	    	];
    	}
        return response()->json($response);
    }

    

}
