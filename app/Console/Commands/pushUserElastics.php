<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class pushUserElastics extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:name';

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
        $bar = $this->output->createProgressBar(1);

        $bar->start();
        $data = DB::table('users')->orderBy('use_id','ASC')->offset(0)->limit(100000)->get();
        $params = ['body' => []];
        foreach($data as $row) {
            $params['body'][] = [
                'index' => [
                    '_index' => env('APP_NAME_KEY','').'_users',
                    '_type' => 'users',
                    '_id' => $row->use_id."_done"
                ]
            ];
            $row->id     = $row->use_id;
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
