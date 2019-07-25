<?php

namespace App\Repositories\Journey;
use DB;

class GetJourneyRepository 
{
    public function __construct()
    {
        
    }

    public function getJourney($did_not_option_id,$not_chieu_di,$did_loai_xe,$tuy_id){
        if($did_not_option_id){
            $data = $this->getTimeTripToOption($did_not_option_id,$tuy_id,$did_loai_xe);

            if($not_chieu_di == 2){
                
                $data = array_reverse($data);
                $timeRun = 0;
                foreach ($data as $key => $value) {
                    $dataTemp[] = array(
                        'erp_place_id'        =>$value['erp_place_id'],
                        'erp_place_name'      =>$value['erp_place_name'],
                        'erp_time_run'        =>$timeRun,
                        'erp_order_no'        =>$value['erp_order_no'],
                        'erp_is_pickup'       =>$value['erp_is_pickup'],
                        'erp_is_charge'       =>$value['erp_is_charge'],
                        'erp_parent_place_id' =>$value['erp_parent_place_id'],

                    );
                    $timeRun = $value['erp_time_run'];
                }
            }else{
                $dataTemp = $data;
            }
            $result = $dataTemp;
            //$result['journey']['message'] = 'Thành công Option';

        }else{
            
            $data = $this->getTimeToRoute($tuy_id);
            if($not_chieu_di == 2){
               
                $data = array_reverse($data);
                $timeRun = 0;
                foreach ($data as $key => $value) {

                    $dataTemp[] = array(
                        'erp_place_id'        =>$value['erp_place_id'],
                        'erp_place_name'      =>$value['erp_place_name'],
                        'erp_time_run'        =>$timeRun,
                        'erp_order_no'        =>$value['erp_order_no'],
                        'erp_is_pickup'       =>$value['erp_is_pickup'],
                        'erp_is_charge'       =>$value['erp_is_charge'],
                        'erp_parent_place_id' =>$value['erp_parent_place_id'],

                    );
                    $timeRun = $value['erp_time_run'];
                }
            }else{
                $dataTemp = $data;
            }

        }
        return $dataTemp;
    }

    public function getTimeTripToOption($id,$tuy_id = 0,$did_loai_xe=0){
        $data = DB::table('routing_option')->join('routing_option_detail','roo_id','=','rod_routing_option_id')
        ->join('ben_xe','bex_id','=','rod_ben_xe_id')
        ->where('rod_tuyen_id',$tuy_id)
        ->where('rod_dich_vu_id',$did_loai_xe)
        ->where('roo_id',$id)->get();
        $arrayReturnTemp = array();
        $arrayReturn = array();

        if(count($data)){
            foreach ($data as $key => $value) {
                $arrayReturnTemp[$value->bex_id] = array(
                    'erp_place_id'        =>$value->bex_id,
                    'erp_place_name'      =>$value->bex_ten,
                    'erp_time_run'        =>$value->rod_time_run,
                    'erp_is_charge'       =>$value->bex_kinh_doanh,
                    'erp_is_pickup'       =>$value->rod_active,
                    'erp_parent_place_id' =>$value->bex_parent_id,

                );
            }
        }
        $dataDiem = DB::table('tuyen_diem_don_tra_khach')->where('tdd_tuyen_id',$tuy_id)->orderby('tdd_order','ASC')->get();

        foreach ($dataDiem as $key => $value) {
            $bex_id = $value->tdd_bex_id;
            if(isset($arrayReturnTemp[$bex_id])){
                $arrayReturnTemp[$bex_id]['erp_order_no'] = $value->tdd_order;
                $arrayReturn[] = $arrayReturnTemp[$bex_id];
            }
        }

        return $arrayReturn;

    }
    public function getTimeToRoute($tuy_id){
        $data = DB::table('tuyen_diem_don_tra_khach')
        ->join('ben_xe','bex_id','=','tdd_bex_id')
        ->where('tdd_tuyen_id',$tuy_id)->orderby('tdd_order','ASC')->get();
        $arrayReturn = array();

        if(count($data)){
            foreach ($data as $key => $value) {
                $arrayReturn[] = array(
                    'erp_place_id'        =>$value->bex_id,
                    'erp_place_name'      =>$value->bex_ten,
                    'erp_time_run'        =>$value->tdd_thoi_gian,
                    'erp_order_no'        =>$value->tdd_order,
                    'erp_is_charge'       =>$value->bex_kinh_doanh ==1 ? true : false, // điểm tính tiền
                    'erp_is_pickup'       =>true,
                    'erp_parent_place_id' =>$value->bex_parent_id,
                );
            }
        }
        return $arrayReturn;
    }

}
