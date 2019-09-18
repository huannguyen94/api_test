<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\TripsErpSanJob;
use Amqp,DB;
use Exception;



class MQTripErp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:amqp-trip-erp';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Message queue trip erp';

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

        $dataMerchant = $this->getInfoMerchant();
        $merchant_id = isset($dataMerchant['merchant_id']) ? $dataMerchant['merchant_id'] : 0;
        $queue_trip_erp_temp =  $merchant_id.'-queue-trip-erp';
        Amqp::consume("$queue_trip_erp_temp", function ( $message, $resolver) use ($merchant_id){
            $routingKey = $message->get('routing_key');
            if($routingKey ==$merchant_id.'-routing-trip-erp'){
               // \Log::info('activation',['user' => '1111']);
                $dataJson = $message->body;
                $data = json_decode($dataJson);
                $type = $data->type;

                $merchant_id_out = (isset($data->merchant_id) && $data->merchant_id !='') ? $data->merchant_id : 0;
               
                if ( $merchant_id_out == $merchant_id && $merchant_id > 0 ){
                    $arrTrip = $data->payload->trip_id;
                    $this->info("trip.auto.not");
                   
                    if($type=='trip.auto.not'){
                        $arrTrip = DB::table('dieu_do_temp')->select('did_id as trip_id')->where('did_time','>=',time())->get();
                    }
                    foreach ($arrTrip as $key => $trip_id) {
                     
                        dispatch(new TripsErpSanJob($trip_id,$merchant_id));
                    }
                    $resolver->acknowledge($message);
                    $resolver->stopWhenProcessed();
                }else{
                    \Log::error('Lỗi ID nhà xe không hợp lệ');
                    throw new \Exception('Lỗi ID nhà xe không hợp lệ');
                }



            }
        }, [
            'exchange' =>$merchant_id.'-trip_events_erp',
            'routing'  =>$merchant_id."-queue-trip-erp.*",
        ]);
    }
    public function getAllTrip(){
        $arrTrip = DB::table('dieu_do_temp')->select('did_id as trip_id')->where('did_time','>=',time())->get();

        return $arrTrip;
    }
    public function getInfoMerchant(){

        $check = DB::table('configuration')->select('con_id','con_data')->where('con_id',1)->first();
        $check = Collect($check)->toArray();
        $con_data      = $check['con_data'];
        $arrDataConFig = base64_decode($con_data);
        $arrDataConFig = json_decode($arrDataConFig,true);
        $merchant_id = isset($arrDataConFig['con_merchant_id']) ? $arrDataConFig['con_merchant_id'] : 0;
        $arrReturn = array(
            'merchant_id'  => $merchant_id,

        );

        return $arrReturn;
    }
}
