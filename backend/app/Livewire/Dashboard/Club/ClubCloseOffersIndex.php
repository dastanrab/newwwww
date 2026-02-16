<?php

namespace App\Livewire\Dashboard\Club;

use App\Models\Club;
use Livewire\Attributes\Title;
use Livewire\Component;

class ClubCloseOffersIndex extends Component
{
    public $breadCrumb = [['باشگاه مشتریان','d.club.items'],['کدهای استفاده شده بسته','d.club.offers']];
    #[Title('باشگاه مشتریان > کدهای استفاده شده بسته')]

    public function render()
    {
        $this->authorize('club_offer_index', Club::class);
        return view('livewire.dashboard.club.club-close-offers-index');
    }
}
