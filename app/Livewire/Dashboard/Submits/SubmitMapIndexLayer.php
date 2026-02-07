<?php

namespace App\Livewire\Dashboard\Submits;

use App\Models\Car;
use App\Models\City;
use App\Models\Fava;
use App\Models\Location;
use App\Models\Polygon;
use App\Models\Submit;
use App\Models\User;
use App\Models\Driver;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;

class SubmitMapIndexLayer extends Component
{
    #[Url]
    public $date;
    public $driverId;
    #[Url]
    public $driver;
    /**
     * @var \Illuminate\Foundation\Application|\Illuminate\Session\SessionManager|int|mixed|null
     */
    private  $city_id;
    private  $city_name;

    public function boot()
    {
        $this->city_id = session('city',1) ;
        if ($this->city_id == 0){
            $this->city_id=1;
        }
        $this->city_name=City::query()->where('id',$this->city_id)->first()->title;
    }
    public function render()
    {
        return view('livewire.dashboard.submits.submit-map-index-layer');
    }


    #[Computed]
    public function polygons()
    {
        return Polygon::all();
    }
    public function driver_submits($id,$date)
    {
        return Driver::query()->where('user_id',$id)->where('city_id',$this->city_id)->whereDate('created_at',$date)->get()->pluck('submit_id');
    }
    #[Computed]
    public function driver_info()
    {
           if (isset($this->driver) and $this->driver != ''){
               $user=User::query()->where('id',$this->driver)->first();
               $car_id=@Car::query()->where('user_id',$this->driver)->first()->id;
               $location=Location::query()->where('car_id',$car_id)->latest()->first();
               return collect(['id'=>$this->driver,'name'=>$user->name,'lastname'=>$user->lastname,'lat'=>@$location->lat,'lon'=>@$location->long,'created_at'=>@$location->created_at]);
           }
           return null;
    }

    #[Computed]
    public function instants()
    {
        $date = $this->date ? verta()->parse($this->date)->toCarbon() : now();
        $submits=Submit::where('status', 1)->where('city_id',$this->city_id)->where('is_instant', true)->whereDate('start_deadline', $date)
            ->with(['user', 'address' => function ($query) {
                $query->withTrashed();
            }]);
        if (isset($this->driver) and $this->driver != '')
        {
            $ids=$this->driver_submits($this->driver,$date)??[];
            $submits=$submits->whereIn('id',$ids);
        }
        return $submits->get();
    }

    #[Computed]
    public function submits9()
    {
        $date = $this->date ? verta()->parse($this->date)->toCarbon() : now();
        $submits = Submit::where('status', 1)->where('city_id',$this->city_id)->where('start_deadline', $date->hour(9)->startOfHour())
            ->with(['user', 'address' => function ($query) {
                $query->withTrashed();
            }]);
        if (isset($this->driver) and $this->driver != '')
        {
            $ids=$this->driver_submits($this->driver,$date)??[];
            $submits=$submits->whereIn('id',$ids);
        }
        return $submits->get();
    }

    #[Computed]
    public function submits11()
    {
        $date = $this->date ? verta()->parse($this->date)->toCarbon() : now();
        $submits = Submit::where('status', 1)->where('city_id',$this->city_id)->where('start_deadline', $date->hour(11)->startOfHour())
            ->with(['user', 'address' => function ($query) {
                $query->withTrashed();
            }]);
        if (isset($this->driver) and $this->driver != '')
        {
            $ids=$this->driver_submits($this->driver,$date)??[];
            $submits=$submits->whereIn('id',$ids);
        }
        return $submits->get();
    }

    #[Computed]
    public function submits13()
    {
        $date = $this->date ? verta()->parse($this->date)->toCarbon() : now();
        $submits = Submit::where('status', 1)->where('city_id',$this->city_id)->where('start_deadline', $date->hour(13)->startOfHour())
            ->with(['user', 'address' => function ($query) {
                $query->withTrashed();
            }]);
        if (isset($this->driver) and $this->driver != '')
        {
            $ids=$this->driver_submits($this->driver,$date)??[];
            $submits=$submits->whereIn('id',$ids);
        }
        return $submits->get();
    }
    #[Computed]
    public function submits15()
    {
        $date = $this->date ? verta()->parse($this->date)->toCarbon() : now();
        $submits = Submit::where('status', 1)->where('city_id',$this->city_id)->where('start_deadline', $date->hour(15)->startOfHour())
            ->with(['user', 'address' => function ($query) {
                $query->withTrashed();
            }]);
         if (isset($this->driver) and $this->driver != '')
         {
             $ids=$this->driver_submits($this->driver,$date)??[];
             $submits=$submits->whereIn('id',$ids);
         };
        return $submits->get();
    }

    #[Computed]
    public function submits17()
    {
        $date = $this->date ? verta()->parse($this->date)->toCarbon() : now();
        $submits = Submit::where('status', 1)->where('city_id',$this->city_id)->where('start_deadline', $date->hour(17)->startOfHour())
            ->with(['user', 'address' => function ($query) {
                $query->withTrashed();
            }]);
        if (isset($this->driver) and $this->driver != '')
        {
            $ids=$this->driver_submits($this->driver,$date)??[];
            $submits=$submits->whereIn('id',$ids);
        }
        return $submits->get();
    }


    #[Computed]
    public function actives()
    {
        $date = $this->date ? verta()->parse($this->date)->toCarbon() : now();
        $submits=Submit::where('status', 2)->where('city_id',$this->city_id)->whereDate('end_deadline', $date)
            ->with(['user', 'address' => function ($query) {
                $query->withTrashed();
            }]);
        if (isset($this->driver) and $this->driver != '')
        {
            $ids=$this->driver_submits($this->driver,$date)??[];
            $submits=$submits->whereIn('id',$ids);
        }
        return $submits->get();
    }

    #[Computed]
    public function done()
    {
        $date = $this->date ? verta()->parse($this->date)->toCarbon() : now();
        /*return Submit::whereDate('end_deadline', $date)->where('status', 3)
            ->with(['user', 'address' => function ($query) {
                $query->withTrashed();
            }])->get();*/
        $submits= Submit::select(['*','user_id as u_id'])
            ->addSelect(DB::raw('(SELECT count(*) FROM submits WHERE user_id = u_id AND status = 3) as count_submits'))
            ->whereDate('end_deadline', $date)->where('status', 3)
            ->where('city_id',$this->city_id)
            ->with(['user', 'address' => function ($query) {
                $query->withTrashed();
            }]);
        /*if(auth()->id() == developerId()){
            dd($res->toRawSql());
        }*/
        if (isset($this->driver) and $this->driver != '')
        {
            $ids=$this->driver_submits($this->driver,$date)??[];
            $submits=$submits->whereIn('id',$ids);
        }
        return $submits->get();
        // SELECT *,user_id as u_id,(SELECT count(*) FROM submits WHERE user_id = u_id AND status = 3) as count_submits FROM `submits` WHERE status = 3
    }


    #[On('date')]
    public function date($date)
    {
        $this->date = $date;
    }
    #[On('driver')]
    public function driver($driver)
    {
        $this->driver = $driver;
    }
    #[On('city')]
    public function city($city)
    {
        $this->city_id = session('city',1) ;
        if ($this->city_id == 0){
            $this->city_id=1;
        }
        $this->city_name=City::query()->where('id',$this->city_id)->first()->title;

        return redirect(request()->header('Referer'));

    }
}
