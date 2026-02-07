<?php

namespace App\Livewire\Dashboard\Stats;

use App\Models\Driver;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class StatWarehouseDriverIndexList extends Component
{
    use WithPagination;
    #[Url]
    public $date;
    public function render()
    {
        $today=false;
        if($this->date){
            $date = toGregorian($this->date,'/','-',false);
            if ($date == date('Y-m-d'))
            {
                $today=true;
            }
        }
        else{
            $today=true;
            $date = date('Y-m-d');
        }
        $drivers_current_weight= DB::table('drivers as d')
            ->join(
                DB::raw('(SELECT MAX(created_at) AS latest, user_id FROM warehouse_dailies GROUP BY user_id) as t'),
                function($join) {
                    $join->on('d.user_id', '=', 't.user_id')
                        ->whereColumn('d.collected_at', '>', 't.latest');
                }
            )
            ->select('d.user_id', DB::raw('SUM(d.weights) as weight'))
            ->groupBy('d.user_id')
            ->get();
        $user_ids= $this->drivers->pluck('id')->toArray();
        $drivers_latest_collect_date=Driver::select('user_id', DB::raw('MAX(collected_at) as collected_at'))->whereIn('user_id',$user_ids)->where('status', 3)->groupBy('user_id')->get();
        $drivers_weights=Driver::select('user_id', DB::raw('sum(weights) as weight'))->whereIn('user_id',$user_ids)->whereBetween('collected_at', [$date.' 00:00:00', $date.' 23:59:59'])->groupBy('user_id')->get();
        if ($today)
        {
            return view('livewire.dashboard.stats.stat-warehouse-driver-index-list', compact('drivers_current_weight','drivers_weights','drivers_latest_collect_date'));
        }
        return view('livewire.dashboard.stats.stat-warehouse-driver-index-list-old', compact('drivers_current_weight','drivers_weights','drivers_latest_collect_date'));
    }

    #[Computed]
    public function drivers()
    {

        $users =  User::with(['roles','cars', 'drivers' => function ($query) {
            $query->whereDate('collected_at',isset($this->date)?toGregorian($this->date,'/','-',false):date('Y-m-d'));
        }, 'warehouseDailies' => function ($query) {
            $query->whereDate('created_at',isset($this->date)?toGregorian($this->date,'/','-',false):date('Y-m-d'));
        },'rollcalls' => function ($query) {
            $query->whereDate('start_at',isset($this->date)?toGregorian($this->date,'/','-',false):date('Y-m-d'));
        }])->whereHas('roles', function ($query) {
            $query->where('name', 'driver');
        })
            ->whereHas('cars', function ($query) {
                $query->where('is_active', 1);
            })
            ->orderBy('created_at', 'desc')->paginate(50);
        return $users;
    }

    #[On('date')]
    public function date($date)
    {
        $this->resetPage();
        $this->date = $date;
    }
}
