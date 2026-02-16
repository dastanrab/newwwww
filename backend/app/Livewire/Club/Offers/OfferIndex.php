<?php

namespace App\Livewire\Club\Offers;

use Livewire\Attributes\Title;
use Livewire\Component;

class OfferIndex extends Component
{
    public $breadCrumb = [
        ['تخفیف ها','cl.offers']
    ];
    #[Title('تخفیف ها')]

    public function render()
    {
        return view('livewire.club.offers.offer-index');
    }
}
