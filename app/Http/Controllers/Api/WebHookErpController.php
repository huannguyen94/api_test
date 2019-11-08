<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\Seats\SeatRepository;
use App\Repositories\Trips\GetTripInfoRepository;
use App\Jobs\pushElasticsJob;
use Amqp, DB;


class WebHookErpController extends Controller
{
	public function __construct(SeatRepository $SeatRepository,GetTripInfoRepository $getTripInfoRepository ){
		
        $this->SeatRepository        = $SeatRepository;
        $this->getTripInfoRepository = $getTripInfoRepository;
	}

    protected $validationRules = [
        'day_to' => 'required|date',
        'day_from' => 'required|date',
        
    ];

    protected $validationMessages = [
        'day_to.required'         => 'Ngày bắt buộc',
        'day_to.date'         => 'Không phải định dạng ngày',
        
    ];


    public function webHookErp( Request $request,$id){

        $data = $this->getTripInfoRepository->getData($id,1);
        return $data;
    }
    public function veTrong(Request $request, $id){
    	$data = $this->SeatRepository->getData($id,1);
        return $data;
    }

    public function syncSDG( Request $request){
        try {
            $this->validate($request, $this->validationRules, $this->validationMessages);
            $day_to = $request->get('day_to','');
            $day_from = $request->get('day_from','');
            Amqp::publish('sync-sdg', '{"day_from":"'.$day_from.'","day_to":"'.$day_to.'"}' , ['queue' => 'queue-sync-sdg','exchange' => 'exchange-sync-sdg', 'vhost' => env('ERP_QUEUE_VHOST')]);

            dd('Sinh SDG từ ngày '.$day_from.' đến ngày '.$day_to.' thành công');
        }

        catch (\Exception $e) {
            dd($e);
        }
    }
    public function elactics( Request $request,$id = 0){

        // $limit = 1;
        // $arrTicket = DB::table('ban_ve_xuong_xe')
        // ->select('bvh_id')
        // ->offset($limit*$id)
        // ->limit($limit)
        // ->get();
        // foreach ($arrTicket as $key => $row) {
                     
        //     dispatch(new pushElasticsJob($row->bvh_id))->onQueue('push-data-elastic');
        // }
        // dd('done '. count($arrTicket).' index '. $id);
        // // var_dump($id);
        // // $id++;
        // // sleep(5);
        // // return redirect()->to('/api/elactics/'.$id); 
    }
    

}
