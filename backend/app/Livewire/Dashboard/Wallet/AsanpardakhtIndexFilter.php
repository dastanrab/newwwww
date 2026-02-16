<?php

namespace App\Livewire\Dashboard\Wallet;

use Livewire\Attributes\Url;
use Livewire\Component;

class AsanpardakhtIndexFilter extends Component
{
    #[Url]
    public $search;
    #[Url]
    public $dateFrom;
    #[Url]
    public $dateTo;
    public function render()
    {
        return view('livewire.dashboard.wallet.asanpardakht-index-filter');
    }

    public function updated($property)
    {
        if($property == 'search'){
            $this->dispatch('search',$this->search);
        }
        elseif($property == 'dateFrom'){
            $this->dispatch('dateFrom',$this->dateFrom);
        }
        elseif($property == 'dateTo'){
            $this->dispatch('dateTo',$this->dateTo);
        }
    }
}
