<?php

namespace App\Livewire\Dashboard\Submits;

use App\Models\City;
use App\Models\Submit;
use Carbon\Carbon;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;

class SubmitAllIndex extends Component
{
    public $breadCrumb = [['درخواست ها','d.submits.all']];
    /**
     * @var array|\Illuminate\Foundation\Application|\Illuminate\Session\SessionManager|mixed|mixed[]|null
     */
    private $city_id;

    #[Title('درخواست ها')]
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
    public function render()
    {

        $this->authorize('submit_all_index',Submit::class);
        return view('livewire.dashboard.submits.submit-all-index');
    }

    #[Computed]
    public function tomorrowCount($time = 0)
    {
        $query = Submit::whereIn('city_id',$this->city_id);
        if($time){
            $query = $query->where('submits.start_deadline', '=', Carbon::tomorrow()->hour($time)->startOfHour());
        }
        else{
            $query = $query->whereDate('submits.start_deadline', '=', Carbon::tomorrow());
        }
        return $query->where(function ($query) {
            $query->where('status', 1)
                ->orWhere('status', 2);
        })->count();
    }

    #[Computed]
    public function daysCount($day,$time = 0)
    {
        $query = Submit::whereIn('city_id',$this->city_id);
        if($time){
            $query = $query->where('submits.start_deadline', '=', $day->hour($time)->startOfHour());
        }
        else{
            $query = $query->whereDate('submits.start_deadline', '=', $day);
        }
        return $query->where(function ($query) {
            $query->where('status', 1)
                ->orWhere('status', 2);
        })->count();
    }

    #[Computed]
    public function afterTomorrowCount($time = 0)
    {
        $query = Submit::whereIn('city_id',$this->city_id);
        if($time){
            $query = $query->where('submits.start_deadline', '=', Carbon::today()->addDays(2)->hour($time)->startOfHour());
        }
        else{
            $query = $query->whereDate('submits.start_deadline', '=', Carbon::today()->addDays(2));
        }
        return $query->where(function ($query) {
            $query->where('status', 1)
                ->orWhere('status', 2);
        })->count();
    }

    public function twoDaysAfterTomorrowCount($time = 0)
    {
        $query = Submit::whereIn('city_id',$this->city_id);
        if($time){
            $query = $query->where('submits.start_deadline', '=', Carbon::today()->addDays(2)->hour($time)->startOfHour());
        }
        else{
            $query = $query->whereDate('submits.start_deadline', '=', Carbon::today()->addDays(2));
        }
        return $query->where(function ($query) {
            $query->where('status', 1)
                ->orWhere('status', 2);
        })->count();
    }
}
