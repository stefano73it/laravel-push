<?php

namespace Demo\SendPush\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Demo\SendPush\PushMessage;
use App\User;
use AWS;
use Str;

class SendPush implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $push;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(PushMessage $push) {
        $this->push = $push;

        // define job queue
        $this->queue = 'sendpush';
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {
      // load ARN tokens from users
    	$users = User::select(array('push_arn_token'))->whereNotNull('push_arn_token')->get();
    	$tokens = array();
    	foreach ($users as $user) {
    		$tokens[] = $user['push_arn_token'];
    	}
    	if (empty($tokens)) return;

      // init SQS Client
    	$sqsClient = AWS::createClient('sqs');

      // create push message body
    	$messageBody = strip_tags(preg_replace('/<br\s?\/?>/i', "\r\n", $this->push->body));
    	$messageBody = Str::limit($messageBody, 250);

      // set push parameters
    	$params = array(
    		'DelaySeconds' => 0,
    		'MessageAttributes' => array(
    			'title' => array('DataType' => 'String', 'StringValue' => $this->push->title),
    			'tokens' => array('DataType' => 'String', 'StringValue' => implode(';', array_unique($tokens))),
    			'badge' => array('DataType' => 'Number', 'StringValue' => 0),
    			'contentType' => array('DataType' => 'String', 'StringValue' => 'push'),
    			'contentId' => array('DataType' => 'Number', 'StringValue' => $this->push->id),
          'callbackUrl' => array('DataType' => 'String', 'StringValue' => $this->push->callback_url),
    		),
    		'MessageBody' => $messageBody,
    		'QueueUrl' => env('SQS_QUEUE'),
    	);

      // send push
    	try {
    		$sqsClient->sendMessage($params);
    	}
    	catch (AwsException $e) {
    		Log::error($e->getMessage());
    	}
    }
}
