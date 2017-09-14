<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

Route::get('/', 'HomeController@index')->name("main");

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::get('/messages/process', 'MessagesController@process');
Route::post('/voice/outbound/{file_name}', 'MessagesController@voiceOutbound');
Route::match(['get', 'post'], '/sms/update', 'MessagesController@smsUpdate');
Route::match(['get', 'post'], '/voice/update', 'MessagesController@voiceUpdate');
Route::get('/voice/gather', 'MessagesController@voiceGather');

Route::get('/test', function(){
    dd(json_encode(['action' => url('/voice/gather'), 'method' => 'GET', 'numDigits' => 1]));
});
