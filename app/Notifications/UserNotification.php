<?php

namespace App\Notifications;

use App\Models\Firebase;
use App\Notifications\Channels\TopicNotificationChannel;
use App\Notifications\Channels\UserNotificationChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    use Queueable;

    protected $data;
    protected $platform;

    public function __construct($data,$platform = null)
    {
        $this->data = $data;
        $this->platform = $platform ?? Firebase::defaultPlatform();
    }

    public function via($notifiable)
    {
        return [UserNotificationChannel::class];
    }

    public function toFirebase($notifiable)
    {
        $firebase = $notifiable->firebases()->where('platform',$this->platform)->get();
        return [
            'data'    => $this->data,
            'tokens'   => $firebase ? $firebase->pluck('token') : null,
            'platform' => $this->platform,
        ];
    }
}
