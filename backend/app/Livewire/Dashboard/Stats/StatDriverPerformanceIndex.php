<?php

namespace App\Livewire\Dashboard\Stats;

use Livewire\Attributes\Title;
use Livewire\Component;

class StatDriverPerformanceIndex extends Component
{

    public $breadCrumb = [['کارکرد راننده ها','d.stats.']];
    #[Title('کارکرد راننده ها')]

    public function render()
    {
        $this->authorize('stat_other_index',StatDriverPerformanceIndex::class);
        return view('livewire.dashboard.stats.stat-driver-performance-index');
    }
}
