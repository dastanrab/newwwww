<?php

namespace App\Livewire\Dashboard\Stats;

use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;

class StatTotalUserIndexList extends Component
{
    #[Url]
    public $date;
    public function render()
    {
        return view('livewire.dashboard.stats.stat-total-user-index-list');
    }

    #[Computed]
    public function ranges()
    {
        $ranges = [];
        if($this->date){
            $startLastMonthJ    = verta()->parse($this->date)->format('Y-m-d');
            $endLastMonthJ = verta()->parse($this->date)->endMonth()->format('Y-m-d');
            $ranges[] = $startLastMonthJ;
            while($startLastMonthJ < $endLastMonthJ){
                $startLastMonthJ = verta()->parse($startLastMonthJ)->addDays()->format('Y-m-d');
                $ranges[] = $startLastMonthJ;
            }
        }
        else{
            $startMonthJ  = verta()->startMonth()->format('Y-m-d');
            $nowJ         = verta()->format('Y-m-d');
            $ranges[] = $startMonthJ;
            while($startMonthJ < $nowJ){
                $startMonthJ = verta()->parse($startMonthJ)->addDays()->format('Y-m-d');
                $ranges[] = $startMonthJ;
            }
        }
        return $ranges;
    }

    #[Computed]
    public function totals()
    {
        $ranges = $this->ranges();
        $i = 0;
        $select = '';
        foreach ($ranges as $range){
            $date = verta()->parse($range)->toCarbon()->format('Y-m-d');
            $select .= "(select count(*) FROM users WHERE legal = 1 AND created_at BETWEEN '$date 00:00:00' AND '$date 23:59:59') as legal_$i,";
            $select .= "(select count(*) FROM users WHERE legal = 0 AND created_at BETWEEN '$date 00:00:00' AND '$date 23:59:59') as not_legal_$i,";
            $i++;
        }
        $select = substr_replace($select,'',-1);
        $users = DB::table('users')->selectRaw($select)->first();
        return $users;

    }

    #[On('date')]
    public function date($date)
    {
        $this->date = $date;
    }
}
