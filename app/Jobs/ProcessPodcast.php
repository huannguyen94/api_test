<?php

namespace App\Jobs;

use App\user;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ProcessPodcast implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;

    /**
     * Create a new job instance.
     *
     * @param  user  $user
     * @return void
     */
    public function __construct(user $user)
    {
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @param  AudioProcessor  $processor
     * @return void
     */
    public function handle()
    {
       

        // $us = new User();
        // $us->name = 'TEST';
        // $us->email = 'test@gmail.com';
        // $us->save();
        // Process uploaded user...
    }
}