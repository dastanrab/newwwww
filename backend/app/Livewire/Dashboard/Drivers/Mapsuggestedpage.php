<?php

namespace App\Livewire\Dashboard\Drivers;

use App\Models\Car;
use App\Models\DriverSuggestedRequests;
use App\Models\Location;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Mapsuggestedpage extends Component
{
    public User $driver;
    public $locations = [];

    public function mount()
    {
        $result = DB::table('driver_suggested_requests as s')
            ->join('submits as su', 's.submit_id', '=', 'su.id')
            ->join('addresses as a', 'su.address_id', '=', 'a.id')
            ->select('s.submit_id', 's.start_at', 'a.lat', 'a.lon','s.status')
            ->where('s.driver_id', $this->driver->id)
            ->whereDate('s.created_at', Carbon::now()->format('Y-m-d'))
            ->orderBy('s.id', 'asc')
            ->get();
        $car = Car::query()->where('user_id',$this->driver->id)->first();
        $location = Location::query()
            ->where('car_id',$car->id)
            ->whereDate('created_at', Carbon::now()->format('Y-m-d'))
            ->orderBy('id', 'asc')->first();
        $this->locations = [
            ['status'=>10,'start'=>0,'lat'=>$location->lat,'lon'=>$location->long,'submit_id'=>0, 'index' => 1],
        ];
        $i=2;
        foreach ($result as $sugested) {
            $this->locations[]=['status'=>$sugested->status,'start'=>$sugested->start_at,'submit_id'=>$sugested->submit_id,'lat' => $sugested->lat, 'lon' => $sugested->lon,'index' => $i];
            $i++;
        }
    }
    public function render()
    {
        return view('livewire.dashboard.drivers.map');
    }
}
