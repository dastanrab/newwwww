<?php

namespace App\Notifications\Channels;

use App\Models\Firebase;
use Illuminate\Notifications\Notification;

class TopicNotificationChannel
{
    public function send($notifiable, Notification $notification)
    {
        $res = $notification->toFirebase($notifiable);
        $firebase = new Firebase($res['platform']);
        $firebase->sendToTopic($res['topic'],$res['data']);
    }
}
