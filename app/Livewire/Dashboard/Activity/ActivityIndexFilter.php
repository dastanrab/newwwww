<?php

namespace App\Livewire\Dashboard\Activity;

use Livewire\Attributes\Url;
use Livewire\Component;

class ActivityIndexFilter extends Component
{
    #[Url]
    public $dateFrom;
    #[Url]
    public $dateTo;
    #[Url]
    public $search;
    public function render()
    {
        return view('livewire.dashboard.activity.activity-index-filter');
    }

    public function updated($prop)
    {
        if($prop == 'dateFrom'){
            $this->dispatch('dateFrom',$this->dateFrom);
        }
        elseif($prop == 'dateTo'){
            $this->dispatch('dateTo',$this->dateTo);
        }
        elseif($prop == 'search'){
            $this->dispatch('search',$this->search);
        }
    }
}
