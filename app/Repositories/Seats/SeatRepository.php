<?php

namespace App\Repositories\Seats;
use DB,Amqp;
use Exception;

class SeatRepository
{
    public function __construct()
    {

    }

    public function getCountFreeSeat($trip_id,$sdg_khoa_ban_ve,$loai_so_do,$sdg_so_cho){
       
        $check = $countFreeSeatTemp = DB::table('ban_ve_ve')
                        ->join('dieu_do_temp','bvv_bvn_id','=','did_id')
                        ->where('did_id',$trip_id)->count();

        $countTemp = count($sdg_khoa_ban_ve);
            if(in_array('',$sdg_khoa_ban_ve)){
                    $countTemp--;
            }

        if($check > 0){
            $countTempDung = DB::table('ban_ve_ve')
                        ->join('dieu_do_temp','bvv_bvn_id','=','did_id')
                        ->where('did_id',$trip_id)
                        ->whereNotIn('bvv_number',$sdg_khoa_ban_ve)
                        ->where('bvv_status','>',0)->count();
            // Den nhung cai chua book neu book roi thuoc ghe san thi da vao case dung
            $soGheSan = DB::table('so_do_giuong_chi_tiet')
                     ->join('ban_ve_ve','sdgct_number','=','bvv_number')
                     ->where('bvv_bvn_id',$trip_id)->where('sdgct_san',1)->where('bvv_status',0)->where('sdgct_sdg_id',$loai_so_do)->count();
            $countFreeSeat = $sdg_so_cho - $countTempDung -$soGheSan - $countTemp;

        }else{
            
            
            $soGheSan = DB::table('so_do_giuong_chi_tiet')
                     ->join('ban_ve_ve','sdgct_number','=','bvv_number')
                     ->where('bvv_bvn_id',$trip_id)->where('sdgct_san',1)->where('sdgct_sdg_id',$loai_so_do)->count();

            $countFreeSeat = $sdg_so_cho - $soGheSan - $countTemp;
        }

        $countFreeSeat = $countFreeSeat > 0 ? $countFreeSeat : 0;
        return $countFreeSeat;
    }
    public function getData($trip_id,$merchant_id){
        $data = DB::table('dieu_do_temp')
        ->join('not_tuyen','did_not_id','=','not_id')
        ->join('bv_loai_dich_vu','bvl_id','=','did_loai_xe')
        ->join('so_do_giuong','did_loai_so_do','=','sdg_id')
        ->where('did_id',$trip_id)->first();

        if(is_null($data)){
            throw new \Exception('Không tìm thấy thông tin data với Trip id = '.$trip_id);
        }
        $sdg_khoa_ban_ve  = $data->sdg_khoa_ban_ve;
        $sdg_khoa_ban_ve = explode(',',$sdg_khoa_ban_ve);

        $tongve  = $data->sdg_so_cho;


        $tuy_id                = $data->not_tuy_id;
        $not_ma                = $data->not_ma;
        $did_time_str          = $data->did_time > 0 ? date('Y-m-d',$data->did_time) : 0;
        $did_gio_xuat_ben_that = $data->did_gio_xuat_ben_that;
        $not_chieu_di_text     = $data->not_chieu_di == 1 ? "A" : "B";
        $did_status            = $data->did_status; 
        $sdg_id                = $data->sdg_id;
        $sdg_name              = $data->sdg_name;
        $did_not_option_id     = $data->did_not_option_id;
        $loai_so_do            = $data->did_loai_so_do;

        $countFreeSeat = $this->getCountFreeSeat($trip_id,$sdg_khoa_ban_ve,$loai_so_do,$tongve);
        $dataReturn = array(
            'trip'=> array(
                'erp_trip_info'=>array(
                    'erp_trip_id'               =>$trip_id,
                    'erp_node_time'             =>$data->did_gio_xuat_ben,
                    'erp_wayroad_id'            =>$tuy_id,
                    'erp_node_code'             =>$not_ma,
                    'erp_merchant_id'           =>$merchant_id,
                    'erp_start_date'            =>$did_time_str,
                    'erp_start_datetime'        =>$did_time_str .' ' .$did_gio_xuat_ben_that,
                    'erp_trip_direction'        =>$not_chieu_di_text,
                    'erp_car_type_id'           =>$sdg_id,
                    'erp_car_type_name'         =>$sdg_name,
                    'erp_trip_staus'            =>$did_status,
                    'erp_trip_total_seats'      =>$tongve,
                    'erp_trip_total_free_seats' =>$countFreeSeat,
                ),
                
            )
        );
        $dataReturnTemp = json_encode($dataReturn);
        Amqp::publish('seat.updated', $dataReturnTemp , ['vhost'    => 'havazerp','exchange' =>'trip_events']);

        return response()->json($dataReturn);
    }

}
