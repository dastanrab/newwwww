<?php

namespace App\Livewire\Dashboard\Wallet;

use Livewire\Attributes\Url;
use Livewire\Component;

class AsanpardakhtIndexNav extends Component
{
    #[Url]
    public $status;
    public function render()
    {
        return view('livewire.dashboard.wallet.asanpardakht-index-nav');
    }

    public function filterStatus($value)
    {
        $this->status = $value;
        $this->dispatch('status', status : $value);
    }
}
