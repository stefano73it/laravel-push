# laravel-push

## Overview

Send notifications using Laravel + SQS + SNS.
You must have a running SQS queue, that will trigger the Lambda function which sends push notifications to mobile devices. 

## How to install

- Copy the laravel -> packages direcory into your Laravel root project
- In your .env file set the necessary AWS variables:
  ```
  AWS_ACCESS_KEY_ID=<access_key_id>
  AWS_SECRET_ACCESS_KEY=<secret_access_key>
  AWS_REGION=<region>
  SQS_PREFIX=<sqs_prefix_name>
  SQS_QUEUE=<sqs_queue_name>
  AWS_APPLICATION_ARN=<application_arn>
  ```
- Add the following line in the providers array of your config/app.php
  ```
  Demo\SendPush\Providers\SendPushServiceProvider::class 
  ```
- Add the following line in the aliases array of your config/app.php
  ```
  'AWS' => Aws\Laravel\AwsFacade::class 
  ```
- Run migrations from command line
  ```
  php artisan migrate 
  ```
- Compile the project in the lambda directory using this command:
  ```
  mvn install 
  ```

**Important!**
Push are sent to users having their push_arn_token field filled with the AWS ARN token of their mobile device!

## How it works

- Create the push by sending a POST request to the /create_push endpoint with the title, body and callback_url fields filled:
  ```
  {"title":"Neque porro quisquam est qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit","body":"Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.","callback_url":"http://example.com/demo_sendpush_callback"} 
  ```

The output will contain the push ID, that you can use to send the push by the following request.

- Send a POST request to the /send_push/<push_id> endpoint. This will create a notification in the database for all users, and will also create a job to send the mobile push. 

- Run the job for the mobile push:
  ```
  php artisan queue:work --queue=sendpush --tries=1 --once 
  ```
