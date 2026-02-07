<?php

namespace App\Livewire\Dashboard\Messages;

use App\Models\Message;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class NotificationIndex extends Component
{
    use WithPagination;
    public $breadCrumb = [
        ['پیام های گروهی','d.track.report'],
    ];
    public function render()
    {
        $this->authorize('notification_index',Message::class);
        return view('livewire.dashboard.messages.notification-index');
    }

    #[Computed]
    public function notifications()
    {
        $query = Message::query();
        return $query->latest()->paginate(10);
    }
}
