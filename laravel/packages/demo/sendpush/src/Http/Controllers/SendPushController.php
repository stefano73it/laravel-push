<?php

namespace Demo\SendPush\Controllers;

use App\Http\Controllers\Controller;
use Demo\SendPush\Requests\PushMessageRequest;
use Demo\SendPush\PushMessage;
use Demo\SendPush\Jobs\SendPush;
use Carbon\Carbon;
use App\User;
use Illuminate\Support\Facades\Notification;
use Demo\SendPush\Notifications\SendPushNotification;
//use Illuminate\Contracts\Bus\Dispatcher; // needed to retrieve job ID

class SendPushController extends Controller {
	/**
	 * Create push message
	 *
	 * @param  \Demo\SendPush\Requests\PushMessageRequest  $request
	 * @return \Demo\SendPush\PushMessage
	 */
	public function createPushMessage(PushMessageRequest $request) {
    	$push = PushMessage::create($request->toArray());
    	return $push;
	}

	/**
	 * Send push message
	 *
	 * @param  \Demo\SendPush\PushMessage  $pushmessage
	 * @return \Illuminate\Http\Response
	 */
	public function sendPushMessage($pushmessage) {
    // load recipients and update recipients count
		$users = User::get();
		$pushmessage->sent_at = Carbon::now();
		$pushmessage->recipients_count = $users->count();
		$pushmessage->save();

    // add database notification
		Notification::send($users, new SendPushNotification($pushmessage));

    // create job for sending push
 		$job = new SendPush($pushmessage);
		//$jobId = app(Dispatcher::class)->dispatch($job); // use this command to dispatch and get job ID
 		dispatch($job);

 		return response()->json(['status' => 'ok', 'recipient_count' => $pushmessage->recipients_count]);
	}
}
