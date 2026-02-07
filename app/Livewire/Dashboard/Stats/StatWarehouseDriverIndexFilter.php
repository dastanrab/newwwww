<?php

namespace App\Livewire\Dashboard\Stats;

use Livewire\Attributes\Url;
use Livewire\Component;

class StatWarehouseDriverIndexFilter extends Component
{
    #[Url]
    public $date;
    #[Url]
    public $date_to;

    public function render()
    {
        return view('livewire.dashboard.stats.stat-warehouse-driver-index-filter');
    }

    public function updated($prop)
    {
        if($prop == 'date'){
            $this->dispatch('date',$this->date);
        }

    }
}
