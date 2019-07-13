<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\TestRequest;

class ApiTestController extends Controller
{
    public function testApi(TestRequest $request){
    	return '2222';
    }
}
