<?php

namespace App\Livewire\Dashboard\Stats;

use Livewire\Attributes\Title;
use Livewire\Component;

class StatWarehouseDailyIndex extends Component
{
    public $breadCrumb = [['آمار روزانه بار تحویلی انبار','d.stats.total-cost']];
    #[Title('آمار روزانه بار تحویلی انبار')]
    public function render()
    {
        $this->authorize('stat_warehouse_daily_create',StatSubmitIndex::class);
        return view('livewire.dashboard.stats.stat-warehouse-daily-index');
    }
}
