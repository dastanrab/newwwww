<?php

namespace App\Livewire\Dashboard\Submits;

use App\Models\City;
use App\Models\Submit;
use Carbon\Carbon;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;

class SubmitAllIndexNav extends Component
{
    #[Url]
    public $status;
    #[Url]
    public $time;
    public  $city_id;

    public function boot()
    {
        $this->city_id = session('city',1) ;
        if ($this->city_id == 0){
            $this->city_id=City::all()->pluck('id')->toArray();
        }
        else{
            $this->city_id=[$this->city_id];
        }
    }
    public function render()
    {
        return view('livewire.dashboard.submits.submit-all-index-nav');
    }

    public function pendingCount($time = 0)
    {
        $query = Submit::where('status', 1)->whereIn('city_id',$this->city_id);
        if($time){
            $query->where('start_deadline', Carbon::today()->hour($time)->startOfHour());
        }
        else{
            $query->whereDate('start_deadline', Carbon::today());
        }
        return $query->count();
    }

    public function activeCount($time = 0)
    {
        $query = Submit::where('status', 2)->whereIn('city_id',$this->city_id);
        if($time){
            $query = $query->where('submits.start_deadline', now()->hour($time)->startOfHour());
        }
        else{
            $query = $query->whereDate('submits.start_deadline', '<=', Carbon::today());
        }
        return $query->count();
    }

    public function tomorrowCount($time = 0)
    {
        $query = Submit::whereIn('city_id',$this->city_id);
        if($time){
            $query = $query->where('submits.start_deadline', '>=', Carbon::tomorrow()->hour($time)->startOfHour());
        }
        else{
            $query = $query->whereDate('submits.start_deadline', '>=', Carbon::tomorrow());
        }
        return $query->where(function ($query) {
            $query->where('status', 1)
                ->orWhere('status', 2);
        })->count();
    }

    public function doneCount($time = 0)
    {
        $query = Submit::where('status', 3)->whereIn('city_id',$this->city_id);

        if($time){
            $query = $query->where('submits.start_deadline', Carbon::today()->hour($time)->startOfHour());
        }
        else{
            $query = $query->whereDate('start_deadline', Carbon::today());
        }
        return $query->count();
    }

    public function filterStatus($status)
    {
        $this->dispatch('status', status : $this->status = $status);
    }

    public function filterTime($time)
    {
        $this->dispatch('time', time : $this->time = $time);
    }

    #[On('reset-time')]
    public function resetTime()
    {
        $this->reset('time');
    }
    #[On('city')]
    public function city($city)
    {
        $this->city_id = session('city',1) ;
        if ($this->city_id == 0){
            $this->city_id=City::all()->pluck('id')->toArray();
        }
        else{
            $this->city_id=[$this->city_id];
        }
    }
}
