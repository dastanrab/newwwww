<?php

namespace App\Livewire\Dashboard\Settings;

use Livewire\Attributes\Title;
use Livewire\Component;

class InstantIndex extends Component
{

    public $breadCrumb = [['مناطق فوری','d.settings.instants']];
    #[Title('مناطق فوری')]
    public function render()
    {
       $this->authorize('setting_instant_index',InstantIndex::class);
        return view('livewire.dashboard.settings.instant-index');
    }
}
