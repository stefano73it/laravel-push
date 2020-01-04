<?php

Route::post('create_push', 'Demo\SendPush\Controllers\SendPushController@createPushMessage');
Route::post('send_push/{id}', 'Demo\SendPush\Controllers\SendPushController@sendPushMessage')->where('id', '[0-9]+');