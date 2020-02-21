<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use DB;

class UserController extends Controller
{
    public function getListingUser(Request $request)
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
	    		"user"=>[
					"id"           => $value->adm_id,
					"name"         => $value->adm_name,
					"phone"        => $value->adm_phone,
					"noi_lam_viec" => isset($chi_nhanh[$value->adm_noi_lam_viec]) ? $chi_nhanh[$value->adm_noi_lam_viec] : "",
					"vp_kt"        => ''
	    		]
	    	];
    	}
        return response()->json($response);
    }

}
