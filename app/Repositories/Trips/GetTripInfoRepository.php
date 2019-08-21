<?php

namespace App\Repositories\Trips;
use DB,Amqp;
use App\Repositories\Journey\GetJourneyRepository;
use App\Repositories\Trips\CarAmenitiesRepository;
use App\Repositories\Price\GetPriceRepository;
use Exception;
class GetTripInfoRepository
{
    public function __construct(GetJourneyRepository $GetJourneyRepository, CarAmenitiesRepository $CarAmenitiesRepository, GetPriceRepository $GetPriceRepository)
    {
        $this->getJourneyRepository = $GetJourneyRepository;
        $this->carAmenitiesRepository = $CarAmenitiesRepository;
        $this->getPriceRepository = $GetPriceRepository;

    }

    public function getData($trip_id,$merchant_id){
        $data = DB::table('dieu_do_temp')
        ->join('not_tuyen','did_not_id','=','not_id')
        ->join('bv_loai_dich_vu','bvl_id','=','did_loai_xe')
        ->join('so_do_giuong','did_loai_so_do','=','sdg_id')
        ->where('did_id',$trip_id)->first();

        if(is_null($data)){
            \Log::info('activation',['trip_false' => $trip_id]);
            throw new \Exception('Không tìm thấy thông tin data với Trip id = '.$trip_id);
            return 0;
        }

        $bvo_id                = $data->did_bvo_id;
        $loai_so_do            = $data->did_loai_so_do;
        $did_loai_xe           = $data->did_loai_xe;
        $tuy_id                = $data->not_tuy_id;
        $trip_id               = $data->did_id;
        $not_id                = $data->not_id;
        $not_ma                = $data->not_ma;
        $did_time_str          = $data->did_time > 0 ? date('Y-m-d',$data->did_time) : 0;
        $did_gio_xuat_ben_that = $data->did_gio_xuat_ben_that;
        $not_chieu_di          = $data->not_chieu_di;
        $not_chieu_di_text     = $data->not_chieu_di == 1 ? "A" : "B";
        $did_status            = $data->did_status;
        $bvl_id                = $data->bvl_id;
        $bvl_name              = $data->bvl_name;
        $sdg_id                = $data->sdg_id;
        $sdg_name              = $data->sdg_name;
        $sdg_so_cho       = $data->sdg_so_cho;
        $did_not_option_id     = $data->did_not_option_id;
        $sdg_khoa_ban_ve       = explode(',',$data->sdg_khoa_ban_ve);

        if($did_status !=1){
           $dataReturn = array(
                'trip'=> array(
                    'erp_trip_info'=>array(
                        'erp_trip_id'               =>$trip_id,
                        'erp_node_time'             =>$data->did_gio_xuat_ben,
                        'erp_wayroad_id'            =>$tuy_id,
                        'erp_node_code'             =>$not_ma,
                        'erp_merchant_id'           =>$merchant_id,
                        'erp_trip_staus'            =>$did_status,
                    ),
                    
                )
            );
            $dataReturnTemp = json_encode($dataReturn);
            Amqp::publish('trip.delete', $dataReturnTemp , ['vhost'    => 'havazerp','exchange' =>'trip_events']);
        }


        // include
        $dataPricing   =  $this->getPriceRepository->getDataPrice($tuy_id,$bvo_id,$loai_so_do,$did_loai_xe,$not_chieu_di);
        $dataJourney   =  $this->getJourneyRepository->getJourney($did_not_option_id,$not_chieu_di,$did_loai_xe,$tuy_id);

        $dataAmenities = $this->carAmenitiesRepository->getAmenity($did_loai_xe, $loai_so_do);

        

        $merchant = $this->getMerchant();
        $countTimeTrip = 0;
        $dataJourneyTemp = array();
        $timeTemp = 0;
        $arrTimeTemp = array();
        foreach ($dataJourney as $key => $value) {
            $time = $value['erp_time_run'];
            $countTimeTrip = $countTimeTrip + $time;

            $dataJourneyTemp[]['erp_place_info'] = array(
                'erp_place_id'        =>$value['erp_place_id'],
                'erp_place_name'      =>$value['erp_place_name'],
                'erp_time_run'        =>$value['erp_time_run'],
                'erp_order_no'        =>$value['erp_order_no'],
                'erp_is_pickup'       =>$value['erp_is_pickup'],
                'erp_is_charge'       =>$value['erp_is_charge'],
                'erp_parent_place_id' =>$value['erp_parent_place_id'],

            );
            $timeTemp = $timeTemp + $value['erp_time_run'];
            $arrTimeTemp[$value['erp_place_id']] = $timeTemp;

        }

        $dataPricingTemp =  array();
        foreach ($dataPricing as $key => $value) {
            $dataPricingTemp[]['erp_pricing_info']= array(
                'erp_from'          =>$value['erp_from'],
                'erp_to'            =>$value['erp_to'],
                'erp_time_run_from' =>isset($arrTimeTemp[$value['erp_from']]) ? $arrTimeTemp[$value['erp_from']] : 0,
                'erp_time_run_to'   =>isset($arrTimeTemp[$value['erp_to']]) ? $arrTimeTemp[$value['erp_to']] : 0,
                'erp_base_price'    =>$value['erp_base_price'],
                'erp_min_price'     =>($value['erp_min_price'] > $value['erp_base_price']) ?  $value['erp_base_price'] : $value['erp_min_price'],
                'erp_max_price'     =>($value['erp_max_price'] < $value['erp_base_price']) ? $value['erp_base_price'] : $value['erp_max_price'],

            );
        }
        $countFreeSeat = $this->getCountFreeSeat($trip_id,$sdg_khoa_ban_ve,$loai_so_do,$sdg_so_cho);
        $dataReturn = array(
            'trip'=> array(
                'erp_trip_info'=>array(
                    'erp_trip_id'               =>$trip_id,
                    'erp_node_time'             =>$data->did_gio_xuat_ben,
                    'erp_node_id'               =>$not_id,
                    'erp_wayroad_id'            =>$tuy_id,
                    'erp_node_code'             =>$not_ma,
                    'erp_merchant_id'           =>$merchant_id,
                    'erp_start_date'            =>$did_time_str,
                    'erp_start_datetime'        =>$did_time_str .' ' .$did_gio_xuat_ben_that,
                    'erp_total_time'            =>$countTimeTrip,
                    'erp_trip_direction'        =>$not_chieu_di_text,
                    'erp_car_level_id'          =>$bvl_id,
                    'erp_car_level_name'        =>$bvl_name,
                    'erp_car_type_id'           =>$sdg_id,
                    'erp_car_type_name'         =>$sdg_name,
                    'erp_trip_staus'            =>$did_status,
                    'erp_trip_total_seats'      =>$sdg_so_cho,
                    'erp_trip_total_free_seats' =>$countFreeSeat,
                ),
                'erp_car_amenities' =>$dataAmenities['amenities'],
                'erp_car_imgs'      =>$dataAmenities['images'],
                'journey'           =>$dataJourneyTemp,
                'pricing'           =>$dataPricingTemp
            )
        );


        $dataReturnTemp = json_encode($dataReturn);
        //\Log::info('activation',['user' => $this->trip_id]);

            Amqp::publish('trip.updated', $dataReturnTemp , ['vhost'    => 'havazerp','exchange' =>'trip_events']);

        return response()->json($dataReturn);
    }
    public function getCountFreeSeat($trip_id,$sdg_khoa_ban_ve,$loai_so_do,$sdg_so_cho){

        $check = $countFreeSeatTemp = DB::table('ban_ve_ve')
                        ->join('dieu_do_temp','bvv_bvn_id','=','did_id')
                        ->where('did_id',$trip_id)->count();
        if($check > 0){
            $countFreeSeatTemp = DB::table('ban_ve_ve')
                        ->join('dieu_do_temp','bvv_bvn_id','=','did_id')
                        ->where('did_id',$trip_id)
                        ->whereNotIn('bvv_number',$sdg_khoa_ban_ve)
                        ->where('bvv_status',0)->count();

            $soGheSan = DB::table('so_do_giuong_chi_tiet')
                     ->join('ban_ve_ve','sdgct_number','=','bvv_number')
                     ->where('bvv_bvn_id',$trip_id)->where('sdgct_san',1)->where('sdgct_sdg_id',$loai_so_do)->count();
            $countFreeSeat = $countFreeSeatTemp -$soGheSan;

        }else{
            $soGheSan = DB::table('so_do_giuong_chi_tiet')
                     ->join('ban_ve_ve','sdgct_number','=','bvv_number')
                     ->where('bvv_bvn_id',$trip_id)->where('sdgct_san',1)->where('sdgct_sdg_id',$loai_so_do)->count();
            $countFreeSeat = $sdg_so_cho - $soGheSan - count($sdg_khoa_ban_ve);
        }
        



        // if($trip_id ==316275){
        //     $dataLog = array(
        //         'countFreeSeat'        =>$countFreeSeat,
        //         'soGheSan'             =>$soGheSan,
        //         'trip_id'              =>$trip_id,
        //         'sdg_khoa_ban_ve'      =>$sdg_khoa_ban_ve,
        //         'loai_so_do'           =>$loai_so_do,
              

        //     );
        //     \Log::info('activation',['trip' => $dataLog]);
        // }
        $countFreeSeat = $countFreeSeat > 0 ? $countFreeSeat : 0;
        return $countFreeSeat;
    }
    public  function getMerchant(){
        $check = DB::table('configuration')->where('con_id',1)->first();
        $check = Collect($check)->toArray();
        $con_data      = $check['con_data'];
        $arrDataConFig = base64_decode($con_data);
        $arrDataConFig = json_decode($arrDataConFig,true);
        $con_code = isset($arrDataConFig['con_code']) ? $arrDataConFig['con_code'] : 'Khong_co_du_lieu';
        return $con_code;
    }


}
