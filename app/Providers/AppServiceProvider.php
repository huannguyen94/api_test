<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Queue, DB;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
        Queue::looping(function () {
            while (DB::transactionLevel() > 0) {
                DB::rollBack();
            }
        });

          DB::listen(function ($query) {
            $arrLog = array(
                'sql'      => $query->sql,
                'bindings' => $query->bindings,
                'time'     => $query->time,
            );
            //\Log::info('activation',['logTimeSql' => $arrLog]);
        });
    }
}
