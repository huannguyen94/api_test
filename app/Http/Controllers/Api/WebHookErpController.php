<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\Seats\SeatRepository;


class WebHookErpController extends Controller
{
	public function __construct(SeatRepository $SeatRepository ){
		
		$this->SeatRepository = $SeatRepository;
	}
    public function webHookErp( Request $request,$id){

        $data = $this->getTripInfoRepository->getData($id,1);
        return $data;
    }
    public function veTrong(Request $request, $id){
    	$data = $this->SeatRepository->getData($id,1);
        return $data;
    }
}
