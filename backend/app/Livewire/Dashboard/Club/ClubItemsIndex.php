<?php

namespace App\Livewire\Dashboard\Club;

use App\Models\Club;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

class ClubItemsIndex extends Component
{
    public $breadCrumb = [['باشگاه مشتریان','d.club.items'],['آیتم ها','d.club.items']];
    #[Title('باشگاه مشتریان > آیتم ها')]

    public function render()
    {
        $this->authorize('club_index', Club::class);
        return view('livewire.dashboard.club.club-items-index');
    }

}
