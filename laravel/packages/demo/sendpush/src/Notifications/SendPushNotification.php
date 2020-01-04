<?php

namespace Demo\SendPush\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendPushNotification extends Notification implements ShouldQueue {
    use Queueable;

    protected $push;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($push) {
        $this->push = $push;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable) {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable) {
        return array('title' => $this->push->title, 'body' => $this->push->body);
    }

    /**
     * Get the database representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toDatabase($notifiable) {
    	return array('sendpush_id' => $this->push->id);
    }
}
