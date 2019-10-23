<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;

class SyncElastic extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:elastic';

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
        ini_set('memory_limit','512M');
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $count = DB::table('ban_ve_xuong_xe')->count();
        $count_1 = intval($count/10000) + 1;
        $bar = $this->output->createProgressBar($count_1);

        $bar->start();


        $page = 1; 
        while(true) {
            $offset = ($page -1)*10000;
            
            $data = DB::table('ban_ve_xuong_xe')->orderBy('bvh_id','DESC')->offset($offset)->limit(10000)->get();
            if (count($data) <= 0) break; 
        
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
            sleep(1);
            $bar->advance();
            $page++;
        }

        $bar->finish();

    }
}
