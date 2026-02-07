<?php

namespace App\Livewire\Dashboard\Wallet;

use Livewire\Attributes\Url;
use Livewire\Component;

class WalletDriverIndexFilter extends Component
{
    #[Url]
    public $search;
    public function render()
    {
        return view('livewire.dashboard.wallet.wallet-driver-index-filter');
    }

    public function updated($property)
    {
        if($property == 'search'){
            $this->dispatch('search',$this->search);
        }
    }
}
