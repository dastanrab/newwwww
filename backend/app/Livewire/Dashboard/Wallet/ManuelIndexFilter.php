<?php

namespace App\Livewire\Dashboard\Wallet;

use Livewire\Attributes\Url;
use Livewire\Component;

class ManuelIndexFilter extends Component
{

    #[Url]
    public $dateFrom;

    public function render()
    {
        return view('livewire.dashboard.wallet.manuel-index-filter');
    }
    public function updated($property)
    {
            $this->dispatch('dateFrom',$this->dateFrom);
    }
}
