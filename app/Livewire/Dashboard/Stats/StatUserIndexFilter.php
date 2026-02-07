<?php

namespace App\Livewire\Dashboard\Stats;

use Livewire\Attributes\Url;
use Livewire\Component;

class StatUserIndexFilter extends Component
{
    #[Url]
    public $search;
    #[Url]
    public $isLegal;
    public function render()
    {
        return view('livewire.dashboard.stats.stat-user-index-filter');
    }

    public function updated($property)
    {
        if($property == 'search'){
            $this->dispatch('search',$this->search);
        }
    }
}
