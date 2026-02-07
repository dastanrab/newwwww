<?php

namespace App\Livewire\Dashboard\Stats;

use Livewire\Attributes\Url;
use Livewire\Component;

class StatTotalCostIndexFilter2 extends Component
{
    #[Url]
    public $StartDate;
    #[Url]
    public $EndDate;
    public function render()
    {
        return view('livewire.dashboard.stats.stat-total-cost-index-filter2');
    }


    public function updated($prop)
    {
        if($prop == 'EndDate'){
            $this->dispatch('EndDate',$this->EndDate);
        }
        if($prop == 'StartDate'){
            $this->dispatch('StartDate',$this->StartDate);
        }
    }
}
