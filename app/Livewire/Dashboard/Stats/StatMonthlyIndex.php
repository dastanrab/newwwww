<?php

namespace App\Livewire\Dashboard\Stats;

use Livewire\Attributes\Title;
use Livewire\Component;

class StatMonthlyIndex extends Component
{

    public $breadCrumb = [['آمار ماهانه','d.stats.monthly']];
    #[Title('آمار ماهانه')]

    public function render()
    {
        $this->authorize('stat_monthly_index',StatSubmitIndex::class);
        return view('livewire.dashboard.stats.stat-monthly-index');
    }
}
