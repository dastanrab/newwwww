<?php

namespace App\Livewire\Dashboard\Stats;

use Livewire\Attributes\Title;
use Livewire\Component;

class StatDailyIndex extends Component
{
    public $breadCrumb = [['آمار روزانه','d.stats.user.total']];
    #[Title('آمار روزانه')]
    public function render()
    {
        $this->authorize('stat_daily_index',StatSubmitIndex::class);
        return view('livewire.dashboard.stats.stat-daily-index');
    }
}
