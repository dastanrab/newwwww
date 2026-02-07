<?php

namespace App\Livewire\Dashboard\Drivers;

use App\Models\FailedRollcall;
use App\Models\Polygon;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

class DriverFeiledRollcallMap extends Component
{


    public $breadCrumb = [['مختصات حضور و غیاب','d.drivers.failedRollcall']];
    #[Title('مختصات حضور و غاب')]

    public FailedRollcall $failedRollcall;

    public function render()
    {
        return view('livewire.dashboard.drivers.driver-feiled-rollcall-map');
    }

    #[Computed]
    public function polygons()
    {
        return Polygon::all();
    }


}
