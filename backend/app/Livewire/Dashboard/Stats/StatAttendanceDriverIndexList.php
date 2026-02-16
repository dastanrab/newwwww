<?php

namespace App\Livewire\Dashboard\Stats;

use App\Models\DriversAttendanceLogs;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;

class StatAttendanceDriverIndexList extends Component
{

    #[Url]
    public $dateFrom;
    public function render()
    {
        return view('livewire.dashboard.stats.stat-attendance-driver-index-list');
    }
    #[Computed]
    public function attendances()
    {
        if (isset($this->dateFrom))
        {
            $today=false;
            $date=toGregorian($this->dateFrom,'/','-',false);
        }
        else{
            $date=now();
            $today=true;
        }
        $query = DriversAttendanceLogs::with(['user'])->whereDate('start_at',$date);
        $query=$today?$query->whereNull('end_at'):$query;
        return $query->orderBy('created_at', 'asc')->paginate(15);
    }
    #[On('dateFrom')]
    public function dateFrom($dateFrom)
    {
        $this->dateFrom = $dateFrom;
    }

}
