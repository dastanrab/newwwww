<?php

namespace App\Livewire\Dashboard\Messages;

use App\Models\Firebase;
use App\Models\Message;
use App\Models\User;
use App\Notifications\GroupMessage;
use App\Notifications\TopicNotification;
use Illuminate\Support\Facades\Notification;
use Livewire\Attributes\Title;
use Livewire\Component;

class NotificationCreate extends Component
{
    public $title;
    public $text;
    public $breadCrumb = [
        ['پیام های گروهی','d.notifications'],
        ['ایجاد'],
    ];
    #[Title('پیام های گروهی > ایجاد')]
    public function render()
    {
        $this->authorize('notification_create',Message::class);
        return view('livewire.dashboard.messages.notification-create');
    }

    public function store()
    {
        $user = auth()->user();
        $this->validate(
            [
                'title' => 'required',
                'text' => 'required',
            ],
            [
                'title' => 'عنوان را وارد نمایید',
                'text' => 'متن را وارد نمایید',
            ]
        );
        //
        $data = [
            "title" => $this->title,
            "message" => $this->text,
        ];
//        Notification::send($user,new TopicNotification(Firebase::dataFormat($data),'all'));
        $user->messages()->create(['title' => $this->title, 'text' => $this->text]);
        sendToast(1,'پیام همگانی ارسال شد');
    }
}
