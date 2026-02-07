<?php

namespace App\Livewire\Dashboard\Stats;

use App\Models\Driver;
use App\Models\Recyclable;
use App\Models\User;
use App\Models\WarehouseDaily;
use Carbon\Carbon;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;

class StatWarehouseDriverDetailIndexList extends Component
{
    #[Url]
    public $date;
    public function render()
    {


        return view('livewire.dashboard.stats.stat-warehouse-driver-detail-index-list');
    }


    #[Computed]
    public function recyclables()
    {
        return Recyclable::pluck('title','id');
    }

    #[Computed]
    public function drivers()
    {
        if($this->date){
            $date = verta()->parse($this->date)->toCarbon()->format('Y-m-d');
        }
        else{
            $date = now()->format('Y-m-d');
        }
        $users = User::whereHas('roles', function ($query) {
            $query->where('name', 'driver');
        })
        ->whereHas('car', function ($query) {
            $query->where('is_active', 1);
        })
        ->with(['drivers' => function ($query) use($date){
            $query->whereDate('collected_at', $date)->where('status',3);
        }])->orderBy('created_at','DESC')->get();
        $recyclables = [];
        foreach ($users as $user){
            $recyclables[$user->id]['user'] = $user;
            foreach ($this->recyclables() as $recyclableId => $recyclable) {
                $recyclables[$user->id]['receives'][$recyclableId] = 0;
            }
            $recyclables[$user->id]['total'] = 0;
            foreach ($user->drivers as $driver){
                foreach ($this->recyclables() as $recyclableId => $recyclable) {
                    $recyclables[$user->id]['receives'][$recyclableId] += $driver->receives()->where('fava_id',$recyclableId)->sum('weight');
                    $recyclables[$user->id]['total'] += $driver->receives()->where('fava_id',$recyclableId)->sum('weight');
                }
            }
            if(!isset($recyclables[$user->id]['receives'])){
                foreach ($this->recyclables() as $recyclableId => $recyclable) {
                    $recyclables[$user->id]['receives'][$recyclableId] = 0;
                }
            }
        }
        return $recyclables;
    }

    #[On('date')]
    public function date($date)
    {
        $this->date = $date;
    }


}
