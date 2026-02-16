<?php

namespace App\Livewire\Dashboard\Stats;

use App\Models\User;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;

class StatSubmitIndexFilter extends Component
{
    #[Url]
    public $search;
    #[Url]
    public $driverId;
    #[Url]
    public $type;
    #[Url]
    public $dateFrom;
    #[Url]
    public $dateTo;
    #[Url]
    public $status;
    public function render()
    {
        return view('livewire.dashboard.stats.stat-submit-index-filter');
    }

    #[Computed]
    public function drivers()
    {
        return User::with(['roles','cars', 'drivers'])
            ->whereHas('roles', function ($query) {
                $query->where('name', 'driver');
            })
            ->whereHas('cars', function ($query) {
                $query->where('is_active', 1);
            })
            ->orderBy('created_at', 'desc')->paginate(50);
    }

    public function updated($prop)
    {
        if($prop == 'dateFrom'){
            $this->dispatch('dateFrom',$this->dateFrom);
        }
        elseif($prop == 'dateTo'){
            $this->dispatch('dateTo',$this->dateTo);
        }
        elseif($prop == 'search'){
            $this->dispatch('search',$this->search);
        }
        elseif($prop == 'status'){
            $this->dispatch('status',$this->status);
        }
    }
}
