<?php

namespace App\Livewire\Dashboard\Stats;

use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

class StatAttendanceDriverIndex extends Component
{
    public $breadCrumb = [['آمار حاضر به کار رانندگان','d.stats.attendance-driver']];
    #[Title('آمار حاضر به کار رانندگان')]
    #[Url]
    public $dateFrom;
    #[Url]
    public $dateTo;
    public function render()
    {
      $this->authorize('stat_warehouse_driver_index',StatSubmitIndex::class);
        return view('livewire.dashboard.stats.stat-attendance-driver-index');
    }
}
