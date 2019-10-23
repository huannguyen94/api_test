<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;

class CronSyncElastic extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:sync-elastic';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cron sync elasticsearch';

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
        $bar = $this->output->createProgressBar(1);

        $bar->start();
        $data = DB::table('ban_ve_xuong_xe')->orderBy('bvv_time_cancel','DESC')->limit(10)->get();
        $params = ['body' => []];
        foreach($data as $row) {
            $params['body'][] = [
                'index' => [
                    '_index' => 'ticket_index',
                    '_type' => 'ticket',
                    '_id' => $row->bvh_id."_done"
                ]
            ];
            $row->id     = $row->bvh_id;
            $row->status = 'done';
            $params['body'][] = $row;
        }
        $responses = app('elasticsearch')->bulk($params);
        // erase the old bulk request
        $params = ['body' => []];

        // unset the bulk response when you are done to save memory
        unset($responses);
        $bar->advance();
        $bar->finish();
    }
}
