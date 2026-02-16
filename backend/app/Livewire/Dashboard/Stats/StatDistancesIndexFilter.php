<?php

namespace App\Livewire\Dashboard\Stats;

use Livewire\Attributes\Url;
use Livewire\Component;

class StatDistancesIndexFilter extends Component
{

    #[Url]
    public $date;
    public function render()
    {
        return view('livewire.dashboard.stats.stat-distances-index-filter');
    }

    public function updated($prop)
    {
        if($prop == 'date'){
            $this->dispatch('date', date: $this->date);
        }
    }
}
