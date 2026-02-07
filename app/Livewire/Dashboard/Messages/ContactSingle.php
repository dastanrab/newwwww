<?php

namespace App\Livewire\Dashboard\Messages;

use App\Models\Contact;
use App\Models\Firebase;
use App\Models\User;
use App\Notifications\UserNotification;
use Illuminate\Support\Facades\Notification;
use Livewire\Attributes\Title;
use Livewire\Component;

class ContactSingle extends Component
{
    public $breadCrumb = [['صندوق پیام','d.track.report'],['پاسخ','d.track.report']];
    #[Title('صندوق پیام > پاسخ')]

    public Contact $contact;
    public $replies;
    public $message;

    public function render()
    {
        $this->authorize('contact_single',Contact::class);
        $this->replies = $this->contact->contactReplies;
        $this->contact->update(['admin_seen_at' => now()]);
        return view('livewire.dashboard.messages.contact-single');
    }

    public function store(Contact $contact)
    {
        $this->validate([
            'message' => 'required|string|min:3|max:5000'
        ]);
        $requester = User::find($contact->user_id);
        $contact->contactReplies()->create([
            'user_id' => auth()->id(),
            'message' => $this->message,
        ]);
        $contact->update(['user_seen_at' => null]);

        $data = [
            'title' => 'پیام از پشتیبانی',
            'message' => $requester->name.' عزیز، پیامی برای شما ارسال شد',
        ];
        Notification::send($requester, new UserNotification(Firebase::dataFormat($data)));

        $this->reset('message');
    }
}
