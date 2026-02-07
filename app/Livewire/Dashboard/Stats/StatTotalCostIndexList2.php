<?php

namespace App\Livewire\Dashboard\Stats;

use App\Models\AsanPardakht;
use App\Models\BazistWallet;
use App\Models\Cashout;
use App\Models\Wallet;
use Carbon\Carbon;
use Hekmatinasser\Verta\Verta;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;

class StatTotalCostIndexList2 extends Component
{
    #[Url]
    public $StartDate;
    #[Url]
    public $EndDate;

    public function render()
    {
        $archive=DB::table('financial_archive');
        if(isset($this->StartDate) && isset($this->EndDate)){
            $start = Verta::parse($this->StartDate)->toCarbon()->format('Y-m-d');
            $end = Verta::parse($this->EndDate)->toCarbon()->format('Y-m-d');
            $s = Carbon::parse($start);
            $e = Carbon::parse($end);
            if ($e->gt($s))
            {
                $archive->whereBetween('created_at',[$start,$end]);
            }
            else{
                $start = Carbon::now()->format('Y-m-d');
                $end = Carbon::now()->subDays(20)->format('Y-m-d');
                $archive->whereBetween('created_at',[$start,$end]);
            }

        }
        $archive=$archive->orderBy('created_at', 'desc')->limit(20)->get()->reverse()->values();
        return view('livewire.dashboard.stats.stat-total-cost-index-list2',compact('archive'));
    }


    #[On('EndDate')]
    public function EndDate($date)
    {
        $this->EndDate = $date;
    }
    #[On('StartDate')]
    public function StartDate($date)
    {
        $this->StartDate = $date;
    }

}
