<?php

namespace App\Livewire\Dashboard\Club;

use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;

class ClubItemIndexFilter extends Component
{

    #[Url]
    public $search;

    public function render()
    {
        return view('livewire.dashboard.club.club-item-index-filter');
    }

    public function updated($property)
    {
        if($property == 'search'){
            $this->dispatch('search',$this->search);
        }
    }
}
