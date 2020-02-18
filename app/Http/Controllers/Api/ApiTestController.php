<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\TestRequest;
use App\User;

class ApiTestController extends Controller
{

	public function __construct(User $User){
        $this->user        = $User;
	}
    public function testApi(Request $request){
    	$data = $this->user->first();
    	return response()->json($data);
    }
}
