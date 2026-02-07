<?php

namespace App\Livewire\Dashboard\Settings;

use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;

class InstantIndexFilter extends Component
{
    public $search;
    public function render()
    {
        return view('livewire.dashboard.settings.instant-index-filter');
    }

    public function updated($property)
    {
        if($property == 'search'){
            $this->dispatch('search',$this->search);
        }
        elseif($property == 'isLegal'){
            $this->dispatch('isLegal',$this->isLegal);
        }
    }

    #[On('filter-search')]
    public function search($search)
    {
        $this->search = $search;
    }
}
