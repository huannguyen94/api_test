<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\AdminLv2User;

class AuthController extends Controller
{	
	public function __construct(AdminLv2User $AdminLv2User){
        $this->adminLv2User = $AdminLv2User;
	}
    public function login( Request $request){
    	extract($request->only(['username', 'password','app_token']));

    	$result = [
			'message'    => '',
			'token'      => '',
			'adm_name'   => '',
			'last_login' => '',
			'adm_id'     => '',
    	];
		$username          = mb_strtolower($username,"UTF-8");     	   
		$adm_loginname_md5 = md5($username);
		$dataUser = $this->adminLv2User->where('adm_loginname_md5',$adm_loginname_md5)
								->where('adm_active',1)
								->where('adm_delete',0)->first();
		if(is_null($dataUser)){
		 	$result['message'] = 'Không tồn tại tài khoản';
		    return response()->json($result,401);

		}
	    $adm_hash      = $dataUser->adm_hash;
	    $password      = md5($password . $adm_hash);
	    $adm_password  = $dataUser->adm_password;
	    if($password == $adm_password){
	       $adm_id        = $dataUser->adm_id; 
	       	$strToken = base64_encode($dataUser->adm_name).'.'.base64_encode(date('d-m-Y H:i:s')).'.'.base64_encode($dataUser->adm_id);    
	       	$token = md5($strToken);
	    
			$dataUser->adm_token = $token;
			$check = $dataUser->save();  

			$result['message']    = 'Đăng nhập thành công!';
			$result['adm_id']     = (int)$dataUser->adm_id;
			$result['adm_name']   = $dataUser->adm_name;
			$result['last_login'] = date('d-m-Y H:i:s');
			$result['token']      = $token;
		 	return response()->json($result);

	    }else{
	    	$result['message'] = 'Mật khẩu không đúng';
	    	return response()->json($result,401);
	    }  
		return response()->json($result);
    }
}
