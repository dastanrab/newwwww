<?php

namespace App\Livewire\Dashboard\Club;

use App\Models\Club;
use Livewire\Attributes\Title;
use Livewire\Component;

class ClubCategoriesIndex extends Component
{

    public $breadCrumb = [['باشگاه مشتریان','d.club.items'],['دسته بندی','d.club.categories']];
    #[Title('باشگاه مشتریان > دسته بندی')]

    public function render()
    {
        $this->authorize('club_category_index', Club::class);
        return view('livewire.dashboard.club.club-categories-index');
    }
}
