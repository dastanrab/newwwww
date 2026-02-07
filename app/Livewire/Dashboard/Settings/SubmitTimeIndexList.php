<?php

namespace App\Livewire\Dashboard\Settings;

use App\Models\Day;
use App\Models\Hour;
use App\Models\Polygon;
use App\Models\PolygonDayHour;
use App\Models\SubmitTime;
use Livewire\Attributes\Computed;
use Livewire\Component;

class SubmitTimeIndexList extends Component
{

    public $instant;

    public function mount()
    {
        $this->instant = SubmitTime::find(1)->instant;
    }

    public function render()
    {
        return view('livewire.dashboard.settings.submit-time-index-list');
    }

    #[Computed]
    public function days()
    {
        return Day::all();
    }

    #[Computed]
    public function hours()
    {
        return Hour::all();
    }

    #[Computed]
    public function polygons()
    {
        return Polygon::orderBy('sort')->get();
    }

    #[Computed]
    public function polygonDayHour()
    {
        $polygons = PolygonDayHour::all()->toArray();
        return collect($polygons);
    }

    public function instantUpdate()
    {
        SubmitTime::find(1)->update(['instant' => !$this->instant]);
    }

    public function save($dayId,$hourId,$polygonId,$select = null)
    {
        if ($polygonId == 'all') {
            $polygon_day_hours = PolygonDayHour::where('day_id', $dayId)
                ->where('hour_id', $hourId)->get();
            if ($select == 'select') {
                foreach ($polygon_day_hours as $polygon_day_hour) {
                    $polygon_day_hour->status = true;
                    $polygon_day_hour->save();
                }
            } elseif($select == 'deselect') {
                foreach ($polygon_day_hours as $polygon_day_hour) {
                    $polygon_day_hour->status = false;
                    $polygon_day_hour->save();
                }
            }
        }
        else{
            $polygon_day_hour = PolygonDayHour::where('day_id', $dayId)->where('hour_id', $hourId)->where('polygon_id', $polygonId)->first();
            if($polygon_day_hour && $polygon_day_hour->status == 1){
                $polygon_day_hour->status = false;
                $polygon_day_hour->save();
            }
            else{
                $polygon_day_hour->status = true;
                $polygon_day_hour->save();
            }
        }

        $day_hour_count = PolygonDayHour::where('day_id', $dayId)
            ->where('hour_id', $hourId)
            ->where('status', true)->count();

        if ($day_hour_count == Polygon::count()) {
            'green';
        } elseif ($day_hour_count > 0) {
            'blue';
        } else {
            'red';
        }
    }
}
