<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Amqp;

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
        Amqp::consume('queue-trip-erp', function ( $message, $resolver){
            dump($message->body);
        }, [
            'exchange' =>'amq.topic',
            'routing'  =>"queue-trip-erp.*"
        ]);
    }
}
