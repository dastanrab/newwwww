<?php

namespace App\Livewire\Dashboard\Stats;

use Livewire\Attributes\Title;
use Livewire\Component;

class StatAreaIndex extends Component
{
    public $breadCrumb = [['آمار مناطق','d.stats.area']];
    #[Title('آمار مناطق')]
    public function render()
    {
        return view('livewire.dashboard.stats.stat-area-index');
    }
}
