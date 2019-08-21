<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Repositories\Trips\GetTripInfoRepository;



class TripsErpSanJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public $tries = 3;

    protected $trip_id;
    protected $merchant_id;


    public function __construct($trip_id,$merchant_id)
    {
        $this->trip_id     = $trip_id;
        $this->merchant_id = $merchant_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(GetTripInfoRepository $getTripInfoRepository)
    {
        $data = $getTripInfoRepository->getData($this->trip_id,$this->merchant_id);
       //event(new \App\Events\TripErpSanEvent($this->trip_id));
    }
}
