<?php

namespace App\Livewire\Dashboard\Wallet;

use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;

class CashoutIndexFilter extends Component
{
    #[Url]
    public $search;
    #[Url]
    public $date;
    public function render()
    {
        return view('livewire.dashboard.wallet.cashout-index-filter');
    }

    public function updated($property)
    {
        if($property == 'search'){
            $this->dispatch('search',$this->search);
        }
        if($property == 'date'){
            $this->dispatch('date',$this->date);
        }
    }

    #[On('date')]
    public function status($date)
    {
        $this->date = $date;
    }
}
