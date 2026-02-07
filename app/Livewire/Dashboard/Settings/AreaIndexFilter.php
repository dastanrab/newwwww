<?php

namespace App\Livewire\Dashboard\Settings;

use Livewire\Attributes\Url;
use Livewire\Component;

class AreaIndexFilter extends Component
{
    #[Url]
    public $search;
    public function render()
    {
        return view('livewire.dashboard.settings.area-index-filter');
    }

    public function updated($prop)
    {
        if($prop == 'search') {
            $this->dispatch('search', $this->search);
        }
    }
}
