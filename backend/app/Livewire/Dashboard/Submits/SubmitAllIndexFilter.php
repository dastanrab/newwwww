<?php

namespace App\Livewire\Dashboard\Submits;

use App\Models\User;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;

class SubmitAllIndexFilter extends Component
{
    #[Url]
    public $search;
    #[Url]
    public $status;
    #[Url]
    public $sort;
    #[Url]
    public $city;
    #[Url]
    public $driver;
    public function render()
    {
        return view('livewire.dashboard.submits.submit-all-index-filter',[
    'options' => [
        '1' => 'مشهد',
        '2' => 'طرقبه'
    ],
        ]);
    }

    public function updated($property)
    {
        if($property == 'search'){
            $this->dispatch('search',$this->search);
        }
        elseif($property == 'status'){
            $this->dispatch('status',$this->status);
        }elseif($property == 'city'){
            $this->dispatch('city',$this->city);
        }
    }

    #[Computed]
    public function drivers()
    {
        return User::with('roles')->whereHas('roles', function ($query) {
            $query->where('name', 'driver');
        })->whereHas('cars', function ($query) {
            $query->where('is_active', 1);
        })->orderBy('created_at', 'desc')->get();
    }

    #[On('status')]
    public function status($status)
    {
        $this->status = $status;
    }
    #[On('city')]
    public function city($city)
    {
        $this->city = $city;
    }

    #[On('reset-sort')]
    public function resetSort()
    {
        $this->reset('sort');
    }
}
