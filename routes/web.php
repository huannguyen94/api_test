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


Route::get('/debug-sentry', function () {
    throw new Exception('My first Sentry error!');
});

Route::get('test-job','podCastController@testJob');

Route::get('/', function () {
	// Amqp::publish('routing-key', 'diepbap dayr message diepbap1', [
	// 	'exchange' => 'erp_events1'
	// ]);

	Amqp::publish('routing-trip-erp', '{"type":"trip.bks","payload":{"trip_id":[38507,38508]}}' , ['queue' => 'queue-trip-erp']);
    return view('welcome');
    // echo phpinfo();
});

Route::get('get-queue-san',function(){
	Amqp::consume('queue-trip-san', function ($message, $resolver) {
    		
	   var_dump($message->body);

	   $resolver->acknowledge($message);

	   $resolver->stopWhenProcessed();
	        
	});
});

Route::get('get-queue-erp',function(){
	Amqp::consume('queue-trip-erp', function ($message, $resolver) {
    		
	   var_dump($message->body);

	   $resolver->acknowledge($message);

	   $resolver->stopWhenProcessed();
	        
	});
});
  