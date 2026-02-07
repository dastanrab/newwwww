<?php

namespace App\Livewire\Dashboard\Stats;

use App\Models\Driver;
use App\Models\Polygon;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;

class StatAreaIndexList extends Component
{
    #[Url]
    public $date;
    public function render()
    {
        return view('livewire.dashboard.stats.stat-area-index-list');
    }

    #[Computed]
    public function items()
    {
        $polygons = Polygon::all();
        $data['بدون منطقه'] = [
            'legal' => 0,
            'notLegal' => 0,
        ];
        foreach ($polygons as $polygon){
            $data[$polygon->region] = [
                'legal' => 0,
                'notLegal' => 0,
            ];
        }
        if($this->date){
            $date = verta()->parse($this->date)->toCarbon();
        }
        else {
            $date = today();
        }
        $drivers = Driver::with(['submit.address','submit.user'])->whereDate('collected_at',$date)->where('status',3)->orderByDesc('collected_at')->get();
        foreach ($drivers as $driver){
            if(bazistDistrict([$driver->submit->address->lat,$driver->submit->address->lon])) {
                if ($driver->submit->user->legal == 1) {
                    $data[bazistDistrict([$driver->submit->address->lat, $driver->submit->address->lon])]['legal'] += 1;
                } else {
                    $data[bazistDistrict([$driver->submit->address->lat, $driver->submit->address->lon])]['notLegal'] += 1;
                }
            }
            else{
                if ($driver->submit->user->legal == 1) {
                    $data['بدون منطقه']['legal'] += 1;
                }
                else{
                    $data['بدون منطقه']['notLegal'] += 1;
                }
            }
        }

        return $data;
    }

    #[On('date')]
    public function date($date)
    {
        return $this->date = $date;
    }


}
