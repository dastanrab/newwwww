<?php

namespace App\Livewire\Dashboard\Stats;

use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

class StatSalaryDriverIndex extends Component
{
    public $breadCrumb = [['آمار حقوق رانندگان','d.stats.salary-driver']];
    #[Title('آمار حقوق رانندگان')]
    #[Url]
    public $dateFrom;
    #[Url]
    public $dateTo;
    public function render()
    {
//        $this->authorize('stat_warehouse_driver_index',StatSubmitIndex::class);
        return view('livewire.dashboard.stats.stat-salary-driver-index');
    }
}
