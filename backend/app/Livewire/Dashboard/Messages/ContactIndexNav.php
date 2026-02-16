<?php

namespace App\Livewire\Dashboard\Messages;

use Livewire\Attributes\Url;
use Livewire\Component;

class ContactIndexNav extends Component
{
    #[Url]
    public $status;
    public function render()
    {
        return view('livewire.dashboard.messages.contact-index-nav');
    }

    public function filterStatus($value)
    {
        $this->status = $value;
        $this->dispatch('status', status : $value);
    }
}
