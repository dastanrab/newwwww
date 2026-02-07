<?php

namespace App\Livewire\Dashboard\Stats;

use Livewire\Attributes\Url;
use Livewire\Component;

class StatWasteIndexFilter extends Component
{
    #[Url]
    public $date;
    public function render()
    {
        return view('livewire.dashboard.stats.stat-waste-index-filter');
    }

    public function updated($prop)
    {
        if($prop == 'date'){
            $this->dispatch('date',$this->date);
        }
    }
}
