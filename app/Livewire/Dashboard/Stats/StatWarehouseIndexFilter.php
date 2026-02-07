<?php

namespace App\Livewire\Dashboard\Stats;

use App\Models\User;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;

class StatWarehouseIndexFilter extends Component
{
    #[Url]
    public $dateFrom;
    #[Url]
    public $dateTo;
    #[Url]
    public $carId;
    public function render()
    {
        return view('livewire.dashboard.stats.stat-warehouse-index-filter');
    }

    #[Computed]
    public function drivers()
    {
        $query = User::with(['roles','cars'])->whereHas('roles', function ($query) {
            $query->where('name', 'driver');
        })->whereHas('cars', function ($query) {
            $query->where('is_active', 1);
        });
        return $query->orderBy('created_at', 'DESC')->paginate(20);
    }

    public function updated($prop)
    {
        if($prop == 'dateFrom'){
            $this->dispatch('dateFrom',$this->dateFrom);
        }
        elseif($prop == 'dateTo'){
            $this->dispatch('dateTo',$this->dateTo);
        }
        elseif($prop == 'carId'){
            $this->dispatch('carId',$this->carId);
        }
    }
}
