<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\pushElasticsJob;
use DB;
class pushFullDataEL extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:push-full-data-el';

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
        $arrTicket = DB::table('ban_ve_xuong_xe')
        ->select('bvh_id')
        ->get();
        foreach ($arrTicket as $key => $row) {
                     
            dispatch(new pushElasticsJob($row->bvh_id))->onQueue('push-data-elastic');
        }
    }
}
