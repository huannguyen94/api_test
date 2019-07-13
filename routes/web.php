<?php
use App\Jobs\ProcessPodcast;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
// Route::get('test-url',function(){
// 	echo url()->previous();
// });


Route::get('test-job','podCastController@testJob');

Route::get('/', function () {
	// Amqp::publish('routing-key', 'diepbap dayr message diepbap1', [
	// 	'exchange' => 'erp_events1'
	// ]);

	Amqp::publish('routing-key', 'message diepbap Hahaa1' , ['queue' => 'queue-name']);
    return view('welcome');
    // echo phpinfo();
});

Route::get('get-rabbit',function(){
	Amqp::consume('queue-name', function ($message, $resolver) {
    		
	   var_dump($message->body);

	   $resolver->acknowledge($message);

	   $resolver->stopWhenProcessed();
	        
	});
});
  