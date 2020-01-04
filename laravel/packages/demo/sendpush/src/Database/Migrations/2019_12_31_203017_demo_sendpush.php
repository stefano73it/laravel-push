<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DemoSendpush extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
       	Schema::create('sendpush_messages', function (Blueprint $table) {
      		$table->bigIncrements('id');
      		$table->string('title', 255);
       		$table->text('body');
       		$table->string('callback_url', 255);
       		$table->integer('recipients_count', FALSE, TRUE)->default(0);
       		$table->timestamp('sent_at')->nullable(TRUE);
       		$table->timestamps();
       	});
        Schema::table('users', function($table) {
          $table->string('push_arn_token')->nullable(TRUE);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('sendpush_messages');
    }
}
