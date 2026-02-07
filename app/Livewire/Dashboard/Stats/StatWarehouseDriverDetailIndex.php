<?php

namespace App\Livewire\Dashboard\Stats;

use Livewire\Attributes\Title;
use Livewire\Component;

class StatWarehouseDriverDetailIndex extends Component
{

    public $breadCrumb = [['آمار بار رانندگان','d.stats.warehouse-driver']];
    #[Title('آمار بار رانندگان')]
    public function render()
    {
        $this->authorize('stat_warehouse_driver_index',StatSubmitIndex::class);
        return view('livewire.dashboard.stats.stat-warehouse-driver-detail-index');
    }
}
