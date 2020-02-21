<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use DB;

class UserController extends Controller
{
    public function getListingUser(Request $request)
    {
    	$data_pb = DB::table('admin_lv2_user')->get();
    	foreach ($data_pb as $key => $value) {
	    	$response[] = [	
	    		"user"=>[
					"id"   =>$value->adm_id,
					"name" =>$value->adm_name,
					"ma"   =>$value->adm_ma,
	    		]
	    	];
    	}
    	return response()->json($response);
    }

}
