<?php

namespace App\Notifications\Channels;

use App\Models\Firebase;
use Illuminate\Notifications\Notification;
use Mockery\Exception;

class UserNotificationChannel
{
    public function send($notifiable, Notification $notification)
    {
        $res = $notification->toFirebase($notifiable);
        if(!$res['tokens']){
            throw new Exception('Token Not Exists');
        }
        $firebase = new Firebase($res['platform']);
        foreach ($res['tokens'] as $token) {
            $firebase->send($token,$res['data']);
        }
    }
}
