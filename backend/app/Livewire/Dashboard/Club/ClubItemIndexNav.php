<?php

namespace App\Livewire\Dashboard\Club;

use App\Models\ClubCategory;
use Livewire\Attributes\Url;
use Livewire\Component;

class ClubItemIndexNav extends Component
{
    public $categories;
    #[Url]
    public $category;
    public function render()
    {
        $this->categories = ClubCategory::all();
        return view('livewire.dashboard.club.club-item-index-nav');
    }

    public function filterCategory($value)
    {
        $this->category = $value;
        $this->dispatch('filterCategory', value : $value);
    }


}
