<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use DB;

class UserController extends Controller
{
    public function getListingStaff (Request $request)
    {
    	$limit = isset($request->limit) ? intval($request->limit) : 50;
        if($limit > 100) {
            $limit = 100;
        }

        $page = isset($request->page) ? intval($request->page) : '';

		$data_user      = DB::table('admin_lv2_user')->where('adm_group_id',2)->paginate($limit);
		$data_chi_nhanh = DB::table('chi_nhanh')->get();

		$chi_nhanh = [];
		foreach ($data_chi_nhanh as $key => $value1) {
			$chi_nhanh[$value1->cn_id] = $value1->cn_name;
		}

    	foreach ($data_user as $key => $value) {
	    	$response[] = [	
				"id"           => $value->adm_id,
				"name"         => $value->adm_name,
				"phone"        => $value->adm_phone,
				"noi_lam_viec" => isset($chi_nhanh[$value->adm_noi_lam_viec]) ? $chi_nhanh[$value->adm_noi_lam_viec] : "",
				"vp_kt"        => ''
	    	];
    	}
        return response()->json($response);
    }

    public function putStaff(Request $request)
    {
    	$update = [
			"adm_id"           => $request->adm_id,
			"adm_name"         => $request->adm_name,
			"adm_phone"        => $request->adm_phone,
			"adm_noi_lam_viec" => $request->adm_noi_lam_viec,
		];

    	$data= DB::table('admin_lv2_user')->where('adm_id',$request->adm_id)->update($update);
    	
    	return $data;
    }


    public function getListingBranch (Request $request)
    {
    	$limit = isset($request->limit) ? intval($request->limit) : 50;
        if($limit > 100) {
            $limit = 100;
        }

        $page = isset($request->page) ? intval($request->page) : '';

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
