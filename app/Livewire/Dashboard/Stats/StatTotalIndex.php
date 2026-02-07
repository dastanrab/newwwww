<?php

namespace App\Livewire\Dashboard\Stats;

use Livewire\Attributes\Title;
use Livewire\Component;

class StatTotalIndex extends Component
{
    public $breadCrumb = [['آمار کل','d.stats.user.total']];
    #[Title('آمار کل')]
    public function render()
    {

        $this->authorize('stat_total_index',StatSubmitIndex::class);
        return view('livewire.dashboard.stats.stat-total-index');
    }
}
