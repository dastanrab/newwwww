<?php

namespace App\Livewire\Dashboard\Stats;

use App\Models\City;
use App\Models\Receive;
use App\Models\Recyclable;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;

class StatWasteIndexList extends Component
{
    #[Url]
    public $date;

    public function boot()
    {
        $this->city_id = session('city',1) ;
        if ($this->city_id == 0){
            $this->city_id=1;
        }
    }
    public function mount()
    {
        if(!$this->date){
            $this->date = verta()->format('Y/m/d');
            $this->dispatch('date',$this->date);
        }
    }

    public function render()
    {
        return view('livewire.dashboard.stats.stat-waste-index-list');
    }

    #[Computed]
    public function wastes()
    {
        $wastes = Recyclable::all();
        return $wastes;
    }

    #[Computed]
    public function receives()
    {
        $date = verta()->parse($this->date)->toCarbon();
        $receives = Receive::whereDate('created_at', $date)->whereHas('driver', function ($query) {
            $query->where('city_id', $this->city_id);
        })->get();
        return $receives;
    }

    #[On('date')]
    public function date($date)
    {
        $this->date = $date;
    }
    #[On('city')]
    public function city($city)
    {
        $this->city_id = session('city',1) ;
        if ($this->city_id == 0){
            $this->city_id=1;
        }
    }
}
