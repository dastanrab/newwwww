<?php

namespace App\Livewire\Dashboard\Supervisor;

use App\Models\Car;
use App\Models\Driver;
use App\Models\Location;
use App\Models\Recyclable;
use App\Models\Submit;
use App\Models\User;
use App\Models\WarehouseDaily;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

class SupervisorDriverSingle extends Component
{

    use WithPagination;

    public $breadCrumb = [['رانندگان','d.supervisor.drivers'], ['ویرایش راننده','d.supervisor.drivers.single']];
    #[Title('رانندگان > جزئیات راننده')]

    public User $driver;
    public $name;
    public $lastname;
    public $gender;
    public $mobile;
    public $city;
    public $referral_code;
    public $type;
    public $status;
    public $plaque1;
    public $plaque2;
    public $plaque3;
    public $plaque4;

    public function mount()
    {
        $this->getData($this->driver);
    }

    public function render()
    {
        return view('livewire.dashboard.supervisor.supervisor-driver-single');
    }

    #[Computed]
    public function presenter()
    {
        return $this->driver->referral_code ? User::find(User::getUserIdByReferral($this->driver->referral_code)) : '';
    }

    #[Computed]
    public function alphabet()
    {
        return Car::alphabet();
    }
    #[Computed]
    public function types()
    {
        return Car::types();
    }

    protected function getData($user)
    {
        $this->name = $user->name;
        $this->lastname = $user->lastname;
        $this->gender = $user->gender;
        $this->mobile = $user->mobile;
        $this->city = $user->city->title;
        $this->referralCode = $user->referral_code;
        $this->type = $this->types()->where('name',$user->cars()->first()->type_id)->first()->label;
        $this->status = $user->cars()->first()->is_active ? 'active' : 'deactive';
        $this->plaque1 = $user->cars()->first()->plaque_1;
        $this->plaque2 = $user->cars()->first()->plaque_2;
        $this->plaque3 = $user->cars()->first()->plaque_3;
        $this->plaque4 = $user->cars()->first()->plaque_4;
    }

    #[Computed]

    public function submits()
    {
        $city = 1;
        $query = Submit::where('submits.city_id',$city)
        ->where('submits.status', 2)
        ->whereHas('drivers.user', function (Builder $query) {
            $query->where('id', $this->driver->id);
        })
        ->with('user', 'drivers')->with(['address' => function ($query) {
            $query->withTrashed();
        }])
        ->orderBy('submits.end_deadline');
        //dd($query->toRawSql());
        return $query->get();

    }


    #[Computed]
    public function submitsDone()
    {
        $query = Submit::query();
        $city = 1;
        $query->when($city, function ($query, $city) {
            return $query->where('city_id', $city);
        })
        ->orderBy('start_deadline', 'desc')
        ->with(['user', 'drivers', 'drivers.user', 'drivers.receives'])
        ->with(['address' => function ($query) {
            $query->withTrashed();
        }])
        ->where('status', 3)
        ->whereHas('drivers.user', function (Builder $query) {
            $query->where('id',$this->driver->id);
        })
        ->whereHas('drivers', function (Builder $query) {
            $query->whereDate('collected_at',now()->format('Y-m-d'));
        });

        if(auth()->user()->isDeveloper()) {
            //dd($query->toRawSql());
        }
        return $query->get();
    }


    #[Computed]
    public function locations()
    {
        $date = today();
        $locations = [];
        $car = Car::where('user_id', $this->driver->id)->first();
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
        return $locations;
    }

    #[Computed]
    public function recyclables()
    {
        return Recyclable::all();
    }

    #[Computed]
    public function collected()
    {
        $collected = Driver::where('user_id', $this->driver->id)->where('status', 3)->whereDate('collected_at', today());
        return $collected->orderBy('collected_at','desc')->with('user', 'receives')->get();
    }

}
