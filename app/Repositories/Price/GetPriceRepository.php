<?php

namespace App\Repositories\Price;
use DB;

use App\Repositories\Price\FlexiblePriceRepository;

class GetPriceRepository 
{
    public function __construct(FlexiblePriceRepository $FlexiblePriceRepository)
    {
        $this->flexiblePriceRepository = $FlexiblePriceRepository;
    }
 
    public function getDataPrice($tuy_id,$bvo_id,$loai_so_do,$did_loai_xe,$not_chieu_di){

        if($bvo_id){
            $priceConfig =  $this->flexiblePriceRepository->getPirceMinMax($bvo_id,$loai_so_do,$did_loai_xe);
            $phan_tram_chang_min      = $priceConfig['phan_tram_chang_min'];
            $phan_tram_toan_tuyen_min = $priceConfig['phan_tram_toan_tuyen_min'];
            $tien_giam_chang_min      = $priceConfig['tien_giam_chang_min'];
            $tien_giam_toan_tuyen_min = $priceConfig['tien_giam_toan_tuyen_min'];


            $phan_tram_chang_max      = $priceConfig['phan_tram_chang_max'];
            $phan_tram_toan_tuyen_max = $priceConfig['phan_tram_toan_tuyen_max'];
            $tien_giam_chang_max      = $priceConfig['tien_giam_chang_max'];
            $tien_giam_toan_tuyen_max = $priceConfig['tien_giam_toan_tuyen_max'];
            $hinh_thuc                = $priceConfig['hinh_thuc'];
        }
        

        $data = $this->getPriceChild($tuy_id,$not_chieu_di);
        
        
        $arrReturn = array();
        foreach ($data as $key => $value) {
            $point_a  = $value['point_a'];
            $point_b  = $value['point_b'];
            $price    = $value['price'];
            $priceConfigMinTemp = 0;
            $priceConfigMaxTemp = 0;

            $priceMin = $priceMax = $price;
            $is_full = $this->getToanTuyenOrChang($tuy_id,$point_a,$point_b);
            if($bvo_id){
                if($is_full){

                    if($hinh_thuc == 1){
                        $priceConfigMinTemp = intval(ceil((($phan_tram_toan_tuyen_min / 100) * $price) / 5000) * 5000);
                        $priceConfigMaxTemp = intval(ceil((($phan_tram_toan_tuyen_max / 100) * $price) / 5000) * 5000);
                    }else{
                        $priceConfigMinTemp = $tien_giam_toan_tuyen_min;
                        $priceConfigMaxTemp = $tien_giam_toan_tuyen_max;
                    }
                }else{

                    if($hinh_thuc == 1){
                        $priceConfigMinTemp  = intval(ceil((($phan_tram_chang_min / 100) * $price) / 5000) * 5000);
                        $priceConfigMaxTemp  = intval(ceil((($phan_tram_chang_max / 100) * $price) / 5000) * 5000);
                    }else{
                        $priceConfigMinTemp  = $tien_giam_chang_min;
                        $priceConfigMaxTemp  = $tien_giam_chang_max;
                    }
                }
            }
            

            // Note: Khi số tiền or phần trăm cấu hàng càng lớn thì số tiền thực trả lại càng nhỏ và ngược lại
            $priceMax = $price-$priceConfigMinTemp;
            $priceMin = $price-$priceConfigMaxTemp;

            $arrReturn[] = array(
                'erp_from'          =>$point_a,
                'erp_to'            =>$point_b,
                'erp_time_run_from' =>0,
                'erp_time_run_to'   =>0,
                'erp_base_price'    =>$price,
                'erp_min_price'     =>$priceMin,
                'erp_max_price'     =>$priceMax,

            );
        }
        return $arrReturn;

    }

    public function getPrice(){
        $did_loai_xe = 1;
        $tuy_id      = 1;

        $dataGiaVe     = DB::table('ban_ve_gia')->where('bvg_type',$did_loai_xe)->where('bvg_tuyen_id',$tuy_id)->get();
        $arrReturn     = array();

        foreach ($dataGiaVe as $key => $value) {
            $point_a = $value->bvg_bex_id_a;
            $point_b = $value->bvg_bex_id_b;
            $price   = $value->bvg_bvd_id;

            $arrReturn[$point_a][$point_b] = $price;

        }
        return $arrReturn;
    }
    public function getPriceChild($tuy_id,$not_chieu_di){

        $dataPrice   = $this->getPrice();
        
        $orderBy = 'ASC'
        if($not_chieu_di == 2){
            $orderBy = 'DESC';
        }
        // $arrParent = $this->getPointParent();
        $data = DB::table('tuyen_diem_don_tra_khach')
                ->JOIN('ben_xe','bex_id','=','tdd_bex_id')->where('tdd_tuyen_id',$tuy_id)
                ->orderBy('tdd_order',$orderBy)->get();
        $arrReturn = array();
        foreach ($data as $key => $value) {
            $point_a        = $value->bex_id;
            $bex_kinh_doanh = $value->bex_kinh_doanh;
            $bex_parent_id  = $value->bex_parent_id;
            if($bex_kinh_doanh ==0 && $bex_parent_id > 0){
                $point_a_temp = $bex_parent_id;
                
            }else{
                $point_a_temp = $point_a;
            }
            foreach ($data as $key1 => $value1) {
                $point_b         = $value1->bex_id;
                $bex_kinh_doanh1 = $value1->bex_kinh_doanh;
                $bex_parent_id1  = $value1->bex_parent_id;
                if($bex_kinh_doanh1 ==0 && $bex_parent_id1 > 0){
                    $point_b_temp = $bex_parent_id1;
                }else{
                    $point_b_temp = $point_b;
                }

                $price = isset($dataPrice[$point_a_temp][$point_b_temp]) ? $dataPrice[$point_a_temp][$point_b_temp] : 0;
                $arrReturn[] = array(
                    'point_a' =>$point_a,
                    'point_b' =>$point_b,
                    'price'   => $price,
                );
            }
        }
        return $arrReturn;

    }
    // public function getPointParent(){
    //     $arrReturn = array();
    //     $data = DB::table('ben_xe')->where('bex_kinh_doanh',1)->where('bex_parent_id','>',0)->get();

    //     foreach ($data as $key => $value) {
    //         $arrReturn[$value->bex_parent_id] = $value->bex_id;
    //     }
        
    //     return $arrReturn;

    // }
    function getToanTuyenOrChang($tuy_id, $bvv_bex_id_a, $bvv_bex_id_b) 
    {
        // true: toàn tuyến, false: chặng

        $dataBV  = DB::table('tuyen_diem_don_tra_khach')->join('ben_xe','bex_id','=','tdd_bex_id')->where('tdd_tuyen_id',$tuy_id)->orderBy('tdd_order','ASC')->get();
        $diem_ban_1 = 0;
        $diem_ban_2 = 0;
        $arrDiem1   = array();
        $arrDiem2   = array();

        $dataBV = collect($dataBV);
        foreach ($dataBV as $key => $value) {
            $bex_id  = $value->bex_id;

            if($value->bex_kinh_doanh == 1 && $diem_ban_1 == 0){
               $diem_ban_1 = $bex_id;
            }
            if($value->bex_kinh_doanh == 1){
               $diem_ban_2 = $bex_id;
            }
        }

        $arrDiem1[$diem_ban_1]  = $diem_ban_1;
        $arrDiem2[$diem_ban_2]  = $diem_ban_2;


        foreach($dataBV as $keyBV => $rowBV){
            $bex_id  = $rowBV->bex_id;
            $bex_parent_id = $rowBV->bex_parent_id;
            if($diem_ban_1 > 0 && $bex_parent_id == $diem_ban_1){
                $arrDiem1[$bex_id]  = $bex_id;
            }
            if($diem_ban_2 > 0 && $bex_parent_id == $diem_ban_2){
                $arrDiem2[$bex_id]  = $bex_id;      
            }
        }
     
        if( in_array($bvv_bex_id_a,$arrDiem1) && in_array($bvv_bex_id_b,$arrDiem2) ){
            return true;
        }else if( in_array($bvv_bex_id_a,$arrDiem2) && in_array($bvv_bex_id_b,$arrDiem1) ){
            return true;
        }
        return false;
    }

    
}
