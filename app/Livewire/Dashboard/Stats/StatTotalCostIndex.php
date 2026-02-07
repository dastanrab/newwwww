<?php

namespace App\Livewire\Dashboard\Stats;

use Livewire\Attributes\Title;
use Livewire\Component;

class StatTotalCostIndex extends Component
{
    public $breadCrumb = [['آمار آرشیو مبالغ','d.stats.total-cost']];
    #[Title('آمار آرشیو مبالغ')]
    public function render()
    {
        $this->authorize('stat_total_cost_index',StatSubmitIndex::class);
        return view('livewire.dashboard.stats.stat-total-cost-index');
    }
}
