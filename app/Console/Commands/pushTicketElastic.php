<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;

class pushTicketElastic extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:push-data-elastic';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $bar = $this->output->createProgressBar(1);

        $dataPoint       = $this->getPoint();
        $dataServiceType = $this->serviceType();

        $bar->start();
        $data = DB::table('ban_ve_xuong_xe')
        ->join('dieu_do_temp','bvv_bvn_id','=','did_id')
        ->join('not_tuyen','did_not_id','=','not_id')
        ->join('tuyen','not_tuy_id','=','tuy_id')
        ->orderBy('bvv_time_cancel','DESC')->limit(1)->get();
        $params = ['body' => []];
        foreach($data as $row) {
            $rowTemp = array();
            $arrayTicketType = array(
                '0'=>'Thường',
                '1'=>'Nhanh',

            );

            $rowTemp['merchant_name']              = env('APP_NAME');
            $rowTemp['ticket_id']                  = $row->bvh_id;
            $rowTemp['ticket_station_id_a']        = $row->bvv_bex_id_a;
            $rowTemp['ticket_station_name_a']      = isset($dataPoint[$row->bvv_bex_id_a]['name']) ? $dataPoint[$row->bvv_bex_id_a]['name'] : '';
            $rowTemp['ticket_station_key_a']       = isset($dataPoint[$row->bvv_bex_id_a]['ma']) ? $dataPoint[$row->bvv_bex_id_a]['ma'] : '';
            
            $rowTemp['ticket_station_id_b']        = $row->bvv_bex_id_b;
            $rowTemp['ticket_station_name_b']      = isset($dataPoint[$row->bvv_bex_id_b]['name']) ? $dataPoint[$row->bvv_bex_id_b]['name'] : '';
            $rowTemp['ticket_station_key_b']       = isset($dataPoint[$row->bvv_bex_id_b]['ma']) ? $dataPoint[$row->bvv_bex_id_b]['ma'] : '';
            
            $rowTemp['ticket_type']                = $row->bvv_fast;
            $rowTemp['ticket_type_text']           = isset($arrayTicketType[$row->bvv_fast]) ? $arrayTicketType[$row->bvv_fast] : '';
            $rowTemp['ticket_source']              = $row->bvv_source;
            $rowTemp['ticket_coupon']              = $row->bvv_giam_gia_text;
            $rowTemp['ticket_seat_number']         = $row->bvv_number;
            $rowTemp['ticket_seat_number_name']    = $row->bvv_number_name;
            $rowTemp['ticket_roundtrip_id']        = $row->bvv_khu_hoi_id;
            $rowTemp['ticket_price']               = $row->bvv_price;
            $rowTemp['ticket_price_discount']      = $row->bvv_price_discount;
            $rowTemp['ticket_price_base']          = $row->bvv_price_ly_thuyet;
            $rowTemp['ticket_phone']               = $row->bvv_phone;
            $rowTemp['ticket_customerName_book']   = $row->bvv_ten_khach_hang;
            $rowTemp['ticket_phone_travel']        = $row->bvv_phone_di;
            $rowTemp['ticket_customerName_travel'] = $row->bvv_ten_khach_hang_di;
            $rowTemp['ticket_time_book']           = $row->bvv_time_book > 0 ? date('Y-m-d',$row->bvv_time_book) : '';
            $rowTemp['ticket_startpoint']          = $row->bvv_diem_don_khach;
            $rowTemp['ticket_endpoint']            = $row->bvv_diem_tra_khach;
            $rowTemp['ticket_tranship_a']          = $row->bvv_trung_chuyen_a;
            $rowTemp['ticket_tranship_b']          = $row->bvv_trung_chuyen_b;
            $rowTemp['trip_id']                    = $row->did_id;
            $rowTemp['trip_service_type']          = $row->did_loai_hinh_ban_ve;
            $rowTemp['trip_service_type_type']     = isset($dataServiceType[$row->did_loai_hinh_ban_ve]) ? $dataServiceType[$row->did_loai_hinh_ban_ve] : '';
            
            $rowTemp['trip_start_time']            = $row->did_gio_xuat_ben;
            $rowTemp['trip_start_time_real']       = $row->did_gio_xuat_ben_that;
            $rowTemp['trip_operation_time']        = $row->did_gio_dieu_hanh;
            
            $rowTemp['trip_time']                  = $row->did_time > 0 ? date('Y-m-d',$row->did_time) : '';
            $rowTemp['trip_status']                = $row->did_status;
            $rowTemp['trip_way']                   = $row->not_chieu_di;
            $rowTemp['node_id']                    = $row->not_id;
            $rowTemp['node_code']                  = $row->not_ma;
            $rowTemp['route_id']                   = $row->tuy_id;
            $rowTemp['route_code']                 = $row->tuy_ma;
            $rowTemp['route_name']                 = $row->tuy_ten;
            $rowTemp['route_status']               = $row->tuy_status;
            $rowTemp['status']                     = 'done';



            $rowTemp = (object)$rowTemp;
            $params['body'][] = [
                'index' => [
                    '_index' => env('APP_NAME_KEY','').'_erp_ticket_index',
                    '_type' => 'ticket',
                    '_id' => $row->bvh_id."_done"
                ]
            ];
           
            $row->id     = $row->bvh_id;
            $row->status = 'done';
            $row->time   = date('Y-m-d',$row->did_time).' ' .$row->did_gio_xuat_ben_that;
            $params['body'][] = $row;
        }

        $responses = app('elasticsearch')->bulk($params);
        // erase the old bulk request
        $params = ['body' => []];

        // unset the bulk response when you are done to save memory
        unset($responses);
        $bar->advance();
        $bar->finish();
    }

    public function serviceType(){
        $arrayReturn = array();

        $data  = DB::table('bv_loai_dich_vu')->get();

        foreach ($data as $key => $value) {
            $id = $value->bvl_id;
            $name = $value->bvl_name;

            $arrayReturn[$id] = $name;
        }
        return $arrayReturn;

    }
    public function getPoint(){

        $dataReturn = array();
        $data = DB::table('ben_xe')->get();

        foreach ($data as $key => $value) {
            $id = $value->bex_id;
            $name = $value->bex_ten;
            $ma = $value->bex_ma;
            $dataReturn[$id]['name'] = $name;
            $dataReturn[$id]['ma']   = $ma;

        }

        return $dataReturn;
    }
}
