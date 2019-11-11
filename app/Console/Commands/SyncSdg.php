<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\syncSdgJob;
use DB,Amqp;
class SyncSdg extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:sync-sdg';

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

        // Amqp::consume("queue-sync-sdg", function ( $message, $resolver){

        //     //try{
        //         $dataJson = $message->body;
        //         $data = json_decode($dataJson);
        //         $day_from = strtotime($data->day_from);
        //         $day_to   = strtotime($data->day_to);
        //         $data = DB::table('dieu_do_temp')
        //         ->where('did_time','>=',$day_from)
        //         ->where('did_time','<=',$day_to)
        //         ->where('did_status',1)->get();
        //         foreach ($data as $key => $value) {
        //             $did_loai_so_do = $value->did_loai_so_do;
        //             $did_id = $value->did_id;
        //             dispatch(new syncSdgJob($did_loai_so_do,$did_id))->onQueue('sync-sdg');
        //         }
                
        //         $resolver->acknowledge($message);
        //     // }catch (\Exception $e) {
        //     //     throw new \Exception('Lỗi định dạng ngày');
        //     // }
            

        // }, [
        //     'exchange' =>'exchange-sync-sdg',
        //     'routing'  =>"sync-sdg.*"
        // ]);


        // xử lý lường cron tab

        $day_from = strtotime(date("m-d-Y"));
        $day_to   = strtotime(date('m/d/Y', strtotime('+1 months')));
        $data = DB::table('dieu_do_temp')
        ->where('did_time','>=',$day_from)
        ->where('did_time','<=',$day_to)
        ->where('did_status',1)->get();
        foreach ($data as $key => $value) {
            $did_loai_so_do = $value->did_loai_so_do;
            $did_id = $value->did_id;
            dispatch(new syncSdgJob($did_loai_so_do,$did_id))->onQueue('sync-sdg');
        }
        
        throw new \Exception('cron sơ đồ giường thành công từ ngày'.strtotime(date("m-d-Y")).' đến ngày '. strtotime(date("m-d-Y")) );

    }
}
