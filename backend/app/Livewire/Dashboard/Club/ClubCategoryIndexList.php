<?php

namespace App\Livewire\Dashboard\Club;

use App\Models\ClubCategory;
use Livewire\Attributes\Computed;
use Livewire\Component;

class ClubCategoryIndexList extends Component
{
    public function render()
    {
        return view('livewire.dashboard.club.club-category-index-list');
    }

    #[Computed]
    public function categories()
    {
        $categories = ClubCategory::all();
        return $categories;
    }
}
