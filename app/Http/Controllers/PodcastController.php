<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessPodcast;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\user;

class PodcastController extends Controller
{
    /**
     * Store a new podcast.
     *
     * @param  Request  $request
     * @return Response
     */
    public function testJob(Request $request)
    {
        // Create podcast...
        $user = (\App\user::first());
        dispatch(new ProcessPodcast($user));
    }
}