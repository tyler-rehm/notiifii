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

Auth::routes();
Route::get('/', 'HomeController@index')->name("main");

Route::resource('contact', 'ContactController');
Route::resource('voices', 'VoiceController');
Route::resource('emails', 'EmailController');

Route::resource('message', 'MessageController');
Route::get('/message/process', 'MessageController@process');

Route::resource('sms', 'SmsController');
Route::match(['get', 'post'], '/sms/update', 'MessageController@smsUpdate');

Route::match(['get', 'post'], '/voice/update', 'MessageController@voiceUpdate');
Route::post('/voice/outbound/{file_name}', 'MessageController@voiceOutbound');
Route::get('/voice/gather', 'MessageController@voiceGather');