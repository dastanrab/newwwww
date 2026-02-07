<?php

namespace App\Livewire\Dashboard\Stats;

use Livewire\Attributes\Url;
use Livewire\Component;

class StatTotalCostIndexFilter extends Component
{
    #[Url]
    public $date;
    public function render()
    {
        return view('livewire.dashboard.stats.stat-total-cost-index-filter');
    }

    public function updated($prop)
    {
        if($prop == 'date'){
            $this->dispatch('date',$this->date);
        }
    }
}
