<?php

namespace App\Repositories\Price;
use DB;

class FlexiblePriceRepository 
{
    public function __construct()
    {
        
    }

    public function getPirceMinMax($bvo_id=0, $loai_so_do=0, $did_loai_xe=0 )
    {
        $arrReturn  = array();

        $dataSDGCT = DB::table('so_do_giuong_chi_tiet')
                ->select('sdgct_id','bvog_sdgct_id','bvog_bvo_id','sdgct_sdg_id','bvog_gghg_id')
                ->join('ban_ve_option_sdg','sdgct_id','=','bvog_sdgct_id')
                ->where('bvog_bvo_id',$bvo_id)
                ->where('sdgct_sdg_id',$loai_so_do)->get();

        $dataPriceHangGhe = DB::table('ban_ve_option_price')
                        ->where('bvop_bvo_id',$bvo_id)
                        ->where('bvop_bvl_id',$did_loai_xe)->get();
      
      
        $dataPriceHangGhe = collect($dataPriceHangGhe)->keyBy('bvop_gghg_id');
        

        $phan_tram_chang_min      = 0;
        $phan_tram_toan_tuyen_min = 0;
        $tien_giam_chang_min      = 0;
        $tien_giam_toan_tuyen_min = 0;

        $phan_tram_chang_max      = 0;
        $phan_tram_toan_tuyen_max = 0;
        $tien_giam_chang_max      = 0;
        $tien_giam_toan_tuyen_max = 0;

        $hinh_thuc = 0;
        foreach ($dataSDGCT as $key => $value) {
        
            $price_config_phan_tram_toan_tuyen = 0;
            $price_config_tuyen                = 0;
            $price_config_phan_tram_chang      = 0;
            $price_config_chang                = 0;

            $hang_ghe_id = $value->bvog_gghg_id;

            if($hang_ghe_id != 0){

                $data_calculate = $dataPriceHangGhe[$hang_ghe_id];
                // Xử lý toàn tuyến
                
                $price_config_phan_tram_toan_tuyen = $data_calculate->bvop_phan_tram_toan_tuyen ;
                if($phan_tram_toan_tuyen_min > $price_config_phan_tram_toan_tuyen ){
                    $phan_tram_toan_tuyen_min = $price_config_phan_tram_toan_tuyen;
                }
                if($phan_tram_toan_tuyen_max < $price_config_phan_tram_toan_tuyen ){
                    $phan_tram_toan_tuyen_max = $price_config_phan_tram_toan_tuyen;
                }

                $price_config_tuyen = $data_calculate->bvop_toan_tuyen;

                if($tien_giam_toan_tuyen_min > $price_config_tuyen ){
                    $tien_giam_toan_tuyen_min = $price_config_tuyen;
                }
                if($tien_giam_toan_tuyen_max < $price_config_tuyen ){
                    $tien_giam_toan_tuyen_max = $price_config_tuyen;
                }

                

                // xử lý chạng
                $price_config_phan_tram_chang  = $data_calculate->bvop_phan_tram_chang;

                if($phan_tram_chang_min > $price_config_phan_tram_toan_tuyen ){
                    $phan_tram_chang_min = $price_config_phan_tram_toan_tuyen;
                }
                if($phan_tram_chang_max < $price_config_phan_tram_toan_tuyen ){
                    $phan_tram_chang_max = $price_config_phan_tram_toan_tuyen;
                }

                $price_config_chang  = $data_calculate->bvop_chang;

                if($tien_giam_chang_min > $price_config_chang ){
                    $tien_giam_chang_min = $price_config_chang;
                }
                if($tien_giam_chang_max < $price_config_chang ){
                    $tien_giam_chang_max = $price_config_chang;
                }

                $hinh_thuc = $data_calculate->bvop_hinh_thuc;


            }


        }

        $arrReturn = array(
            'phan_tram_chang_min'      =>$phan_tram_chang_min,
            'phan_tram_toan_tuyen_min' =>$phan_tram_toan_tuyen_min,
            'tien_giam_chang_min'      =>$tien_giam_chang_min,
            'tien_giam_toan_tuyen_min' =>$tien_giam_toan_tuyen_min,
            'phan_tram_chang_max'      =>$phan_tram_chang_max,
            'phan_tram_toan_tuyen_max' =>$phan_tram_toan_tuyen_max,
            'tien_giam_chang_max'      =>$tien_giam_chang_max,
            'tien_giam_toan_tuyen_max' =>$tien_giam_toan_tuyen_max,
            'hinh_thuc'                =>$hinh_thuc,
        );
        dd($arrReturn);
        return $arrReturn;       
    }
}
