<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\Seats\SeatRepository;
use App\Repositories\Trips\GetTripInfoRepository;


class WebHookErpController extends Controller
{
	public function __construct(SeatRepository $SeatRepository,GetTripInfoRepository $getTripInfoRepository ){
		
        $this->SeatRepository        = $SeatRepository;
        $this->getTripInfoRepository = $getTripInfoRepository;
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
