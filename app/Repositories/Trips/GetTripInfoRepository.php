<?php

namespace App\Repositories\Trips;
use DB,Amqp;
use App\Repositories\Journey\GetJourneyRepository;
use App\Repositories\Trips\CarAmenitiesRepository;
use App\Repositories\Price\GetPriceRepository;

class GetTripInfoRepository 
{
    public function __construct(GetJourneyRepository $GetJourneyRepository, CarAmenitiesRepository $CarAmenitiesRepository, GetPriceRepository $GetPriceRepository)
    {
        $this->getJourneyRepository = $GetJourneyRepository;
        $this->carAmenitiesRepository = $CarAmenitiesRepository;
        $this->getPriceRepository = $GetPriceRepository;
        
    }

    public function getData($trip_id){
        $data = DB::table('dieu_do_temp')
        ->join('not_tuyen','did_not_id','=','not_id')
        ->join('bv_loai_dich_vu','bvl_id','=','did_loai_xe')
        ->join('so_do_giuong','did_loai_so_do','=','sdg_id')
        ->where('did_id',$trip_id)->first();

        if(is_null($data)){
            throw new Exception('Không tìm thấy thông tin data với Trip id = '.$trip_id);
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
        $not_chieu_di          = $data->not_chieu_di == 1 ? "A" : "B";
        $did_status            = $data->did_status;
        $bvl_id                = $data->bvl_id;
        $bvl_name              = $data->bvl_name;
        $sdg_id                = $data->sdg_id;
        $sdg_name              = $data->sdg_name;
        $sdg_so_cho_tong       = $data->sdg_so_cho_tong;
        $sdg_so_cho_tong       = $data->sdg_so_cho_tong;
        $did_not_option_id     = $data->did_not_option_id;
        $sdg_khoa_ban_ve       = explode(',',$data->sdg_khoa_ban_ve);


        // include 

        $dataPricing   =  $this->getPriceRepository->getDataPrice($tuy_id,$bvo_id,$loai_so_do,$did_loai_xe);
        
        $dataJourney   =  $this->getJourneyRepository->getJourney($did_not_option_id,$not_chieu_di,$did_loai_xe,$tuy_id);
        
        $dataAmenities = $this->carAmenitiesRepository->getAmenity($did_loai_xe, $loai_so_do);

        $countSeatFree = DB::table('ban_ve_ve')->where('bvv_bvn_id',$trip_id)->whereNotIn('bvv_id',$sdg_khoa_ban_ve)->where('bvv_status',0)->get();

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
        $dataReturn = array(
            'trip'=> array(
                'erp_trip_info'=>array(
                    'erp_trip_id'               =>$trip_id,
                    'erp_node_id'               =>$not_id,
                    'erp_wayroad_id'            =>$tuy_id,
                    'erp_node_code'             =>$not_ma,
                    'erp_start_date'            =>$did_time_str,
                    'erp_start_datetime'        =>$did_time_str .' ' .$did_gio_xuat_ben_that,
                    'erp_total_time'            =>$countTimeTrip,
                    'erp_trip_direction'        =>$not_chieu_di,
                    'erp_car_level_id'          =>$bvl_id,
                    'erp_car_level_name'        =>$bvl_name,
                    'erp_car_type_id'           =>$sdg_id,
                    'erp_car_type_name'         =>$sdg_name,
                    'erp_trip_staus'            =>$did_status,
                    'erp_trip_total_seats'      =>$sdg_so_cho_tong,
                    'erp_trip_total_free_seats' =>count($countSeatFree),
                ),
                'erp_car_amenities' =>$dataAmenities['amenities'],
                'erp_car_imgs'      =>$dataAmenities['images'],
                'journey'           =>$dataJourneyTemp,
                'pricing'           =>$dataPricingTemp
            )
        );
        $dataReturnTemp = json_encode($dataReturn);

        Amqp::publish('routing-trip-san', $dataReturnTemp , ['queue' => 'queue-trip-san']);

        return response()->json($dataReturn);
    }
    

}
