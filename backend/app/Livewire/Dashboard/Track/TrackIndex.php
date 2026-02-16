<?php

namespace App\Livewire\Dashboard\Track;

use App\Models\Car;
use App\Models\Location;
use App\Models\User;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;

class TrackIndex extends Component
{
    public $breadCrumb = [
        ['ردیابی آنلاین','d.track.report'],
    ];

    #[Url]
    public $date;
    #[Url]
    public $userId;

    public function render()
    {

        $this->authorize('track_online_index', TrackReportIndex::class);
        return view('livewire.dashboard.track.track-index');
    }

    public function updated($prop)
    {
        $params = [];
        if($this->date){
            $params['date'] = $this->date;
        }
        if ($this->userId){
            $params['userId'] = $this->userId;
        }
        if($prop){
            $this->redirectRoute('d.track',$params);
        }
    }

    #[Computed]
    public function drivers()
    {
        $drivers = User::with(['cars'])->whereHas('roles', function ($query) {
            $query->where('name', 'driver');
        })->whereHas('cars', function ($query) {
            $query->where('is_active', 1);
        });
        $drivers = $drivers->orderBy('created_at', 'DESC')->get();
        return $drivers;
    }

    #[Computed]
    public function locations()
    {
        $date = $this->date ? verta()->parse($this->date)->toCarbon() : today();
        $locations = [];
        if($this->userId){
            $car = Car::where('user_id', $this->userId)->first();
            $query = Location::where('car_id', $car->id)
                ->whereBetween('date', [$date->hour(6)->startOfHour(), $date->copy()->hour(22)->startOfHour()])
                ->orderBy('date', 'ASC')->with('car.user')->get();
            foreach ($query as $i => $location) {
                $locations[$i] = $location;
                $locations[$i]['user_id'] = $location->car->user->id;
                $locations[$i]['name'] = $location->car->user->name;
                $locations[$i]['lastname'] = $location->car->user->lastname;
                $locations[$i]['date'] = verta()->instance($location->date)->format('Y/m/d H:i');
            }

        }
        else{
            foreach ($this->drivers as $i => $driver) {
                if($location = $driver->cars->first()->locations()->whereDate('date', $date)->orderBy('date', 'DESC')->first()) {
                    $locations[$i] = $location;
                    $locations[$i]['user_id'] = $driver->id;
                    $locations[$i]['name'] = $driver->name;
                    $locations[$i]['lastname'] = $driver->lastname;
                    $locations[$i]['date'] = verta()->instance($location->date)->format('Y/m/d H:i');
                }
            }
        }
        return $locations;
    }
}
