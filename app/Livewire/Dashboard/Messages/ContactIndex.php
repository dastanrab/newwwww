<?php

namespace App\Livewire\Dashboard\Messages;

use App\Models\Contact;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

class ContactIndex extends Component
{
    use WithPagination;
    public $status;
    public $breadCrumb = [
        ['صندوق پیام','d.track.report'],
    ];
    #[Title('صندوق پیام')]
    public function render()
    {
        $this->authorize('contact_index',Contact::class);
        return view('livewire.dashboard.messages.contact-index');
    }

    #[Computed]
    public function contacts()
    {
        $query = Contact::query()->with(['contactReplies'=>function($query)  {
            $query->select(['id','contact_id','created_at'])->latest('created_at')->first();}]);

        if($this->status == 'read'){
            $query = $query->whereNotNull('admin_seen_at');
        }
        elseif($this->status == '' || $this->status == 'unread'){
            $query = $query->whereNull('admin_seen_at');
        }

        return $query->latest()->paginate(10);
    }

    #[On('status')]
    public function status($status)
    {
        $this->resetPage();
        $this->status = $status;
    }
}
