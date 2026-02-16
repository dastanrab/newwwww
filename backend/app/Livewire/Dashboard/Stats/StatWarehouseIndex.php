<?php

namespace App\Livewire\Dashboard\Stats;

use Livewire\Attributes\Title;
use Livewire\Component;

class StatWarehouseIndex extends Component
{

    public $breadCrumb = [['آمار کلی انبار']];
    #[Title('آمار کلی انبار')]
    public function render()
    {
        $this->authorize('stat_warehouse_index',StatSubmitIndex::class);
        return view('livewire.dashboard.stats.stat-warehouse-index');
    }
}
