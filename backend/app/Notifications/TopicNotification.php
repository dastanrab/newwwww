<?php

namespace App\Notifications;

use App\Models\Firebase;
use App\Notifications\Channels\TopicNotificationChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TopicNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $data;
    protected $topic;
    protected $platform;

    public function __construct($data,$topic,$platform = '')
    {
        $this->data = $data;
        $this->topic = $topic;
        $this->platform = $platform ?? Firebase::defaultPlatform();
    }

    public function via($notifiable)
    {
        return [TopicNotificationChannel::class];
    }

    public function toFirebase($notifiable)
    {
        return [
            'data' => $this->data,
            'topic' => $this->topic,
            'platform' => $this->platform,
        ];
    }
}
