<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Amqp;

class PriceTripErp extends Command
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
        Amqp::consume('queue-trip-erp1111', function ( $message, $resolver){

            $routingKey = $message->get('routing_key');
            if($routingKey =='routing-trip-erp'){
                \Log::info('activation',['user' => '222']);
                // $dataJson = $message->body;
                // $data = json_decode($dataJson);
                // $trip_id = 1;
                // event(new \App\Events\TripErpSanEvent($trip_id));
                // if(!is_null($data)){
                    
                //      dump($data);
                // }
                
            }
        }, [
            'exchange' =>'amq.topic',
            'routing'  =>"queue-trip-erp.*"
        ]);
    }
}
