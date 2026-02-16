<?php

namespace App\Livewire\Dashboard\Settings;

use Livewire\Attributes\Title;
use Livewire\Component;

class SubmitTimeIndex extends Component
{

    public $breadCrumb = [['بازه های درخواست','d.settings.area']];
    #[Title('بازه های درخواست')]

    public function render()
    {
        $this->authorize('setting_submit_time_index', SubmitTimeIndex::class);
        return view('livewire.dashboard.settings.submit-time-index');
    }
}
