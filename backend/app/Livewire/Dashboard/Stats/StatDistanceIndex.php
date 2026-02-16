<?php

namespace App\Livewire\Dashboard\Stats;

use Livewire\Attributes\Title;
use Livewire\Component;

class StatDistanceIndex extends Component
{

    public $breadCrumb = [['آمار مسافت','d.stats.distances']];
    #[Title('آمار مسافت')]
    public function render()
    {
        $this->authorize('stat_distance_index',StatSubmitIndex::class);
        return view('livewire.dashboard.stats.stat-distance-index');
    }
}
