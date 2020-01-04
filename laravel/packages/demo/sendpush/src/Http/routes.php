<?php

Route::post('create_push', 'Demo\SendPush\Controllers\SendPushController@createPushMessage');

Route::middleware(['bindings'])->group(function () {
	Route::post('send_push/{pushmessage}', 'Demo\SendPush\Controllers\SendPushController@sendPushMessage');
});