<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\Price\GetPriceRepository;
use App\Repositories\Trips\GetTripInfoRepository;
use App\Repositories\Journey\GetJourneyRepository;

class WebHookErpController extends Controller
{
	public function __construct(GetPriceRepository $GetPriceRepository, GetTripInfoRepository $GetTripInfoRepository, GetJourneyRepository $GetJourneyRepository){
		$this->getPriceRepository = $GetPriceRepository;
		$this->getTripInfoRepository = $GetTripInfoRepository;
		$this->getJourneyRepository = $GetJourneyRepository;
	}
    public function webHookErp( Request $request){

        $data = $this->getTripInfoRepository->getData(1,1);
        return $data;
    }
}
