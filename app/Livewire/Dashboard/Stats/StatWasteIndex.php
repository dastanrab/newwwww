<?php

namespace App\Livewire\Dashboard\Stats;

use Livewire\Attributes\Title;
use Livewire\Component;

class StatWasteIndex extends Component
{
    public $breadCrumb = [['آمار پسماند ها','d.stats.waste']];
    #[Title('آمار پسماند ها')]
    public function render()
    {
        $this->authorize('stat_waste_index',StatSubmitIndex::class);
        return view('livewire.dashboard.stats.stat-waste-index');
    }
}
