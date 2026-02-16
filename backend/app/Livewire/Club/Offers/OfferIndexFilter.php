<?php

namespace App\Livewire\Club\Offers;

use Livewire\Attributes\Url;
use Livewire\Component;

class OfferIndexFilter extends Component
{
    #[Url]
    public $search;
    public function render()
    {
        return view('livewire.club.offers.offer-index-filter');
    }

    public function updated($prop)
    {
        if($prop == 'search'){
            $this->dispatch('search',$this->search);
        }
    }
}
