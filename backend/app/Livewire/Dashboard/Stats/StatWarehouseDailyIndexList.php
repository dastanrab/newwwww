<?php

namespace App\Livewire\Dashboard\Stats;

use App\Models\Driver;
use App\Models\Recyclable;
use App\Models\User;
use App\Models\WarehouseDaily;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;

class StatWarehouseDailyIndexList extends Component
{
    #[Url]
    public $date;
    public function render()
    {
        return view('livewire.dashboard.stats.stat-warehouse-daily-index-list');
    }

    #[Computed]
    public function recyclables()
    {
        return Recyclable::pluck('title');
    }

    #[Computed]
    public function warehouses()
    {
        if($this->date){
            $date = toGregorian($this->date,'/','-',false);
        }
        else{
            $date = now()->format('Y-m-d');
        }
        $dws = WarehouseDaily::whereIn('operator_id', User::azadiId())->whereDate('created_at',$date)->get();
        $collected = [];
        $all = [];
        foreach ($dws as $dw){
            $collected_before = WarehouseDaily::where('user_id',$dw['user_id'])->where('created_at','<', $dw['created_at'])->latest()->first();
            $collected[] = Driver::with('receives')->where('status',3)->where('user_id',$dw['user_id'])->whereBetween('collected_at', [$collected_before['created_at'] , $dw['created_at']])->get();
        }
        if($collected)
            $all = collect($collected)->flatten(1);
        $w1 = $w2 = $w3 = $w4 = $w5 = $w6 = $w7 = $w8 = $w9 = $w10 = $w11 = $w12 = $w13 = $w14 = $w15 = $w16 = $w17 = $w18 = $w19 = $w20 = $w21 = $w22 = [0, 0];

        foreach ($all as $p){
            $w1[0] += $p->receives->where('fava_id', 1)->pluck('weight')->sum();
            $w2[0] += $p->receives->where('fava_id', 2)->pluck('weight')->sum();
            $w3[0] += $p->receives->where('fava_id', 3)->pluck('weight')->sum();
            $w4[0] += $p->receives->where('fava_id', 4)->pluck('weight')->sum();
            $w5[0] += $p->receives->where('fava_id', 5)->pluck('weight')->sum();
            $w6[0] += $p->receives->where('fava_id', 6)->pluck('weight')->sum();
            $w7[0] += $p->receives->where('fava_id', 7)->pluck('weight')->sum();
            $w8[0] += $p->receives->where('fava_id', 8)->pluck('weight')->sum();
            $w9[0] += $p->receives->where('fava_id', 9)->pluck('weight')->sum();
            $w10[0] += $p->receives->where('fava_id', 10)->pluck('weight')->sum();
            $w11[0] += $p->receives->where('fava_id', 11)->pluck('weight')->sum();
            $w12[0] += $p->receives->where('fava_id', 12)->pluck('weight')->sum();
            $w13[0] += $p->receives->where('fava_id', 13)->pluck('weight')->sum();
            $w14[0] += $p->receives->where('fava_id', 14)->pluck('weight')->sum();
            $w15[0] += $p->receives->where('fava_id', 15)->pluck('weight')->sum();
            $w16[0] += $p->receives->where('fava_id', 16)->pluck('weight')->sum();
            $w17[0] += $p->receives->where('fava_id', 17)->pluck('weight')->sum();
            $w18[0] += $p->receives->where('fava_id', 18)->pluck('weight')->sum();
            $w19[0] += $p->receives->where('fava_id', 19)->pluck('weight')->sum();
            $w20[0] += $p->receives->where('fava_id', 20)->pluck('weight')->sum();
            $w21[0] += $p->receives->where('fava_id', 21)->pluck('weight')->sum();
            $w22[0] += $p->receives->where('fava_id', 22)->pluck('weight')->sum();
        }

        $dws = WarehouseDaily::where('operator_id', User::mayameyId())->whereDate('created_at',$date)->get();
        $collected_2 = [];
        $all = [];
        foreach ($dws as $dw){
            $collected_before = WarehouseDaily::where('user_id',$dw['user_id'])->where('created_at','<', $dw['created_at'])->latest()->first();

            $collected_2[] = Driver::with('receives')->where('status',3)->where('user_id',$dw['user_id'])->whereBetween('collected_at', [$collected_before['created_at'] , $dw['created_at']])->get();

        }
        if($collected_2)
            $all = collect($collected_2)->flatten(1);

        foreach ($all as $p){
            $w1[1] += $p->receives->where('fava_id', 1)->pluck('weight')->sum();
            $w2[1] += $p->receives->where('fava_id', 2)->pluck('weight')->sum();
            $w3[1] += $p->receives->where('fava_id', 3)->pluck('weight')->sum();
            $w4[1] += $p->receives->where('fava_id', 4)->pluck('weight')->sum();
            $w5[1] += $p->receives->where('fava_id', 5)->pluck('weight')->sum();
            $w6[1] += $p->receives->where('fava_id', 6)->pluck('weight')->sum();
            $w7[1] += $p->receives->where('fava_id', 7)->pluck('weight')->sum();
            $w8[1] += $p->receives->where('fava_id', 8)->pluck('weight')->sum();
            $w9[1] += $p->receives->where('fava_id', 9)->pluck('weight')->sum();
            $w10[1] += $p->receives->where('fava_id', 10)->pluck('weight')->sum();
            $w11[1] += $p->receives->where('fava_id', 11)->pluck('weight')->sum();
            $w12[1] += $p->receives->where('fava_id', 12)->pluck('weight')->sum();
            $w13[1] += $p->receives->where('fava_id', 13)->pluck('weight')->sum();
            $w14[1] += $p->receives->where('fava_id', 14)->pluck('weight')->sum();
            $w15[1] += $p->receives->where('fava_id', 15)->pluck('weight')->sum();
            $w16[1] += $p->receives->where('fava_id', 16)->pluck('weight')->sum();
            $w17[1] += $p->receives->where('fava_id', 17)->pluck('weight')->sum();
            $w18[1] += $p->receives->where('fava_id', 18)->pluck('weight')->sum();
            $w19[1] += $p->receives->where('fava_id', 19)->pluck('weight')->sum();
            $w20[1] += $p->receives->where('fava_id', 20)->pluck('weight')->sum();
            $w21[1] += $p->receives->where('fava_id', 21)->pluck('weight')->sum();
            $w22[1] += $p->receives->where('fava_id', 22)->pluck('weight')->sum();
        }
        return compact('w1', 'w2', 'w3', 'w4', 'w5', 'w6', 'w7', 'w8', 'w9', 'w10', 'w11', 'w12', 'w13', 'w14', 'w15', 'w16', 'w17', 'w18', 'w19', 'w20', 'w21', 'w22');
    }


    #[On('date')]
    public function date($date)
    {
        $this->date = $date;
    }
}
