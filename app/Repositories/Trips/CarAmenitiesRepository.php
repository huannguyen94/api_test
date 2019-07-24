<?php

namespace App\Repositories\Trips;
use DB;

class CarAmenitiesRepository 
{
    public function __construct()
    {
        
    }
 
    public function getAmenity($did_loai_xe = 0, $did_loai_so_do =0){
        $product_id = $did_loai_xe;
        $diagram_id = $did_loai_so_do;   

        $dataProduct = DB::table('bv_loai_dich_vu')->where('bvl_id',$product_id)->first();   
        $dataDiagram = DB::table('so_do_giuong')->where('sdg_id',$diagram_id)->first();
        $arr_amenities = 
            array_filter(
                array_unique(
                    array_merge(
                        explode(',',$dataProduct ? $dataProduct->bvl_tien_nghi : ''),explode(',',$dataDiagram ? $dataDiagram->sdg_tien_nghi : '')
                    )
                )
            );
        $amenities = DB::table('amenities')->whereIn('ame_id',$arr_amenities)->get();

        //
        $arrImages = explode(',',$dataDiagram->sdg_avatar_list);
        $dataDomain = DB::table('projects')->where('pro_id',0)->first();
        $domain = $dataDomain->pro_link;
        $arrImg = [];
        foreach($arrImages as $key => $value){                 
            $arrImg[] = $domain.$this->getImageFullSize($value);             
        }
        $dataAmenities = [
            'amenities' => collect($amenities)->map(function($item){
                return $item->ame_name;
            }),
            'images' => $arrImg
        ];
        return $dataAmenities;
    }

    function getImageFullSize($sourceFilename){                
        if($sourceFilename == ""){
            $file_path  = "/themes/images/logo.png";
        }else{
            $file_path  = '/pictures/picfullsizes/'. $this->getPathDateTimeImg($sourceFilename);
        }
        return $file_path;
    }

    function getPathDateTimeImg($filename = "", $pre_path = "",$width_height =""){
        $data_tem    = explode("/", $filename);
        $filename     = @end($data_tem);
        $arrName    = explode(".", $filename);
        if(count($arrName) > 1){
            $file_name = $arrName[count($arrName) - 2];
        }
        $time = substr($file_name, -10);

        if($time > time()) $time = time();
        return $pre_path . date("Y/m/d/", $time) . (($width_height != "") ? $width_height . '/' : '') . $filename; 
    }


}
