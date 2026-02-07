<?php

namespace App\Livewire\Dashboard\Club;

use App\Models\Club;
use Livewire\Attributes\Title;
use Livewire\Component;

class ClubOffersIndex extends Component
{
    public $breadCrumb = [['باشگاه مشتریان','d.club.items'],['کدهای استفاده شده','d.club.offers']];
    #[Title('باشگاه مشتریان > کدهای استفاده شده')]

    public function render()
    {
        $this->authorize('club_offer_index', Club::class);
        return view('livewire.dashboard.club.club-offers-index');
    }
}
