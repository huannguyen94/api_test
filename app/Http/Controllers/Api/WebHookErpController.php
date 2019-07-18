<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class WebHookErpController extends Controller
{
    public function webHookErp( Request $request){
        echo "Tôi đã nhận được thông tin của bạn";
    }
}
