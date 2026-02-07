<?php

namespace App\Livewire\Dashboard\Submits;

use App\Models\User;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;

class SubmitMapIndexFilter extends Component
{
    #[Url]
    public $date;
    #[Url]
    public $driver;
    public function render()
    {
        return view('livewire.dashboard.submits.submit-map-index-filter');
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

    public function updated($prop)
    {
        if($prop == 'date'){
            $this->dispatch('date',$this->date);
        }
        if($prop == 'driver'){
            $this->dispatch('driver',$this->driver);
        }
    }
}
