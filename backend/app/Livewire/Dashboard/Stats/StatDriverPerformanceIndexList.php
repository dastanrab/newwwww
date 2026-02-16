<?php

namespace App\Livewire\Dashboard\Stats;

use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class StatDriverPerformanceIndexList extends Component
{
    use WithPagination;
    #[Url]
    public $dateFrom;
    #[Url]
    public $dateTo;
    #[Url]
    public $carId;
    public $row;
    public function render()
    {
        return view('livewire.dashboard.stats.stat-driver-performance-index-list');
    }

    #[Computed]
    public function drivers()
    {
        if ($this->dateFrom && $this->dateTo) {
            $start_date = toGregorian($this->dateFrom,'/','-',false);
            $end_date = toGregorian($this->dateTo,'/','-',false);
        } else {
            $start_date = '2021-01-01';
            $end_date = now()->addDay();
        }
        $results = DB::table('rollcalls')
            ->select('user_id', DB::raw("
        CONCAT(
            FLOOR(SUM(TIMESTAMPDIFF(SECOND, start_at, end_at)) / 3600), ' ساعت و ',
            FLOOR(MOD(SUM(TIMESTAMPDIFF(SECOND, start_at, end_at)), 3600) / 60), ' دقیقه'
        ) AS total_hours_worked,
        CONCAT(
            users.name,' ',
            users.lastname
        ) AS full_name,
        SUM(TIMESTAMPDIFF(SECOND, start_at, end_at)) AS total_seconds
               "))
            ->join('users', 'users.id', '=', 'rollcalls.user_id')
            ->whereNotNull('start_at')
            ->whereNotNull('end_at')
            ->whereBetween('start_at', [$start_date, $end_date])
            ->groupBy('user_id')
            ->orderByDesc('total_seconds')
            ->paginate(60);
        return $results;
    }


    #[On('dateFrom')]
    public function dateFrom($dateFrom)
    {
        $this->resetPage();
        $this->dateFrom = $dateFrom;
    }

    #[On('dateTo')]
    public function dateTo($dateTo)
    {
        $this->resetPage();
        $this->dateTo = $dateTo;
    }

    #[On('row')]
    public function row($row)
    {
        $this->resetPage();
        if(isset($_COOKIE['table-row'])) unset($_COOKIE['table-row']);
        setcookie('table-row',$row,time() + ((86400 * 365))*10, "/"); // 86400 = 1 day
        $this->row = $row;
    }
}
