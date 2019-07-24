<?php

namespace App\Listeners;

use App\Events\TripErpSanEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Repositories\Trips\GetTripInfoRepository;

class TripErpSanListen
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(GetTripInfoRepository $GetTripInfoRepository)
    {
        $this->getTripInfoRepository = $GetTripInfoRepository;
    }

    /**
     * Handle the event.
     *
     * @param  TripErpSanEvent  $event
     * @return void
     */
    public function handle(TripErpSanEvent $event)
    {
        $data = $this->getTripInfoRepository->getTrip();
        \Log::info('data',['user' => $data]);
        $trip_id  = $event->trip_id;
        
    }
}
