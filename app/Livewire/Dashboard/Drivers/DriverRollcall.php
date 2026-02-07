<?php

namespace App\Livewire\Dashboard\Drivers;

use App\Models\Car;
use App\Models\Location;
use App\Models\Rollcall;
use App\Models\User;
use Hekmatinasser\Verta\Verta;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class DriverRollcall extends Component
{
    use WithPagination;
    public $breadCrumb = [['رانندگان','d.drivers'], ['حضور و غیاب','d.drivers.rollcall']];
    #[Title('رانندگان > حضور و غیاب')]

    public User $driver;
    public $hour;
    public $min;
    public $description;
    public $set_location = 0;
    public $lat;
    public $lng;
    public $search;
    #[Url]
    public $dateFrom;
    #[Url]
    public $dateTo;
    public function mount()
    {
        $this->hour = now()->format('H');
        $this->min = now()->format('i');
    }
    public function render()
    {
        $this->authorize('user_driver_index_rollcall',User::class);
        return view('livewire.dashboard.drivers.driver-rollcall');
    }

    public function save()
    {
        $this->validate([
            'hour' => 'required|numeric|between:1,23',
            'min' => 'required|numeric|between:0,59',
            'description' => 'required|min:4',
        ],
        [
            'hour' => 'ساعت را وارد نمایید',
            'min' => 'دقیقه را وارد نمایید',
        ]);
        Rollcall::add([
            'hour' => $this->hour,
            'min' => $this->min,
            'driverId' => $this->driver->id,
            'description' => $this->description
        ]);
        sendToast(1,'حضور ثبت شد');
        $this->dispatch('refresh-list');
    }
    public function saveLocation()
    {
            if (!isset($this->lat) || !isset($this->lng))
            {
                return sendToast(0,'موقعیت راننده را تعیین کنید');
            }
            $car = Car::where('user_id', $this->driver->id)->where('is_active', true)->first();

            $location = new Location;
            $location->car_id = $car->id;
            $location->lat = $this->lat;
            $location->long = $this->lng;
            $location->date = now();
            $location->save();
            $this->dispatch('refresh-list');
            return sendToast(1,'موقعیت ثبت شد');

    }

    public function updated($prop)
    {
        if($prop == 'dateFrom'){
            $this->dispatch('dateFrom', $this->dateFrom);
        }
        elseif ($prop == 'dateTo'){
            $this->dispatch('dateTo', $this->dateTo);
        }
    }
}
