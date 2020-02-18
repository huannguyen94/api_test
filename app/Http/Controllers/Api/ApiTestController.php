<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\TestRequest;
use App\Models\XeModel;

class ApiTestController extends Controller
{

	public function __construct(XeModel $XeModel){
        $this->xeModel        = $XeModel;
	}
    public function testApi(Request $request){
    	$data = $this->xeModel->first();
    	return response()->json($data);
    }
}
