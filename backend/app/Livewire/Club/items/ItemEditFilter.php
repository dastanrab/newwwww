<?php

namespace App\Livewire\Club\items;

use Livewire\Attributes\Url;
use Livewire\Component;

class ItemEditFilter extends Component
{
    #[Url]
    public $search;
    public function render()
    {
        return view('livewire.club.items.item-edit-filter');
    }

    public function updated($prop)
    {
        if($prop == 'search'){
            $this->dispatch('search',$this->search);
        }
    }
}
