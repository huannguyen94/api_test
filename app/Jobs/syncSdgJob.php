<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use DB;
class syncSdgJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    public $tries = 3;
    protected $did_loai_so_do;
    protected $did_id;

    public function __construct($did_loai_so_do,$did_id)
    {
         $this->did_loai_so_do =$did_loai_so_do;
         $this->did_id =$did_id;
        
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //Them giuong neu chua co
        $did_loai_so_do = $this->did_loai_so_do;
        if($did_loai_so_do){
            $query   = "SELECT * FROM so_do_giuong_chi_tiet
                     WHERE sdgct_sdg_id = " . $this->did_loai_so_do;
            $dataLOCho  = DB::select(DB::raw($query));
            $arrCho     = array();
            try {
                $queryInsert   = "INSERT IGNORE INTO ban_ve_ve (bvv_number,bvv_bvn_id,bvv_disable,bvv_warning)
                                  VALUES ";
                $queryInsertVAl   = '';
                foreach($dataLOCho as $key => $row){
                    $queryInsertVAl   .= "(" . $row->sdgct_number . "," . $this->did_id . "," . $row->sdgct_disable . "," . $row->sdgct_warning . "),";              
                }  
                $queryInsertVAl   = trim($queryInsertVAl,",");
                if($queryInsertVAl != ''){
                    $queryInsert   .= $queryInsertVAl;
                    $update = DB::select($queryInsert);
                }
            } catch (\Exception $th) {
                \Log::info("Lỗi không xử lý được sdg".$th);
            } 
        }
        
    }
}
