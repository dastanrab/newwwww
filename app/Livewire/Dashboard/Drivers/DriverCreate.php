<?php

namespace App\Livewire\Dashboard\Drivers;

use App\Events\UserEvent;
use App\Models\Car;
use App\Models\City;
use App\Models\Fava;
use App\Models\Polygon;
use App\Models\User;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

class DriverCreate extends Component
{
    public $breadCrumb = [['رانندگان','d.drivers'],['افزودن راننده','d.drivers.create']];
    #[Title('رانندگان > افزودن راننده')]

    public $step = 1;
    public $search = '';
    public $name;
    public $lastname;
    public $gender;
    public $mobile;
    public $city;
    public $type;
    public $status;
    public $plaque4;
    public $plaque3;
    public $plaque2;
    public $plaque1;
    public $referralCode;
    #[Url]
    public $userId;

    public function render()
    {
        $this->authorize('user_driver_create', User::class);
        return view('livewire.dashboard.drivers.driver-create');
    }

    #[Computed]
    public function genders()
    {
        return User::genders();
    }

    #[Computed]
    public function drivers()
    {
        $query = User::query();
        if($this->search) {
            $query->with('roles')->whereHas('roles', function ($query) {
                $query->whereIn('name', ['supervisor', 'operator', 'accountants', 'marketer', 'user']);
            });

            if (is_numeric($this->search)) {
                $query->where('mobile', 'LIKE', "%{$this->search}%");
            } else {
                $query->whereRaw("CONCAT(name, ' ', lastname) LIKE '%{$this->search}%'");
            }
            $query = $query->orderBy('created_at', 'desc')->paginate(50);
        }
        return $query;
    }

    #[Computed]
    public function presenter()
    {
        return $this->referralCode ? User::find(User::getUserIdByReferral($this->referralCode)) : '';
    }

    public function mount()
    {
        if ($this->userId){
            $user = User::find($this->userId);
            if($user->getRole('name') == 'driver'){
                abort(404);
            }

            $this->getData($user);
        }
    }

    public function selectDriver(User $user)
    {
        $this->step = 2;
        $this->getData($user);
        $this->userId = $user->id;
    }

    public function save()
    {
        $this->validate([
            'type' => 'required|in:1,2,3,4,5,6,7,8,9',
            'status' => 'required|in:active,deactive',
            'plaque1' => 'required|min:2|max:2',
            'plaque2' => 'required',
            'plaque3' => 'required|min:3|max:3',
            'plaque4' => 'required|min:2|max:2',
        ],[
            'type' => 'نوع خودرو را انتخاب نمایید',
            'status' => 'وضعیت را انتخاب کنید',
            'plaque1' => 'پلاک را به درستی وارد کنید',
            'plaque2' => 'پلاک را به درستی وارد کنید',
            'plaque3' => 'پلاک را به درستی وارد کنید',
            'plaque4' => 'پلاک را به درستی وارد کنید',
        ]);
        $car = Car::insert([
            'driverId' => $this->userId,
            'type' => $this->type,
            'isActive' => $this->status == 'active' ? 1 : 0,
            'plaque1' => $this->plaque1,
            'plaque2' => $this->plaque2,
            'plaque3' => $this->plaque3,
            'plaque4' => $this->plaque4,
        ]);
        $this->redirect(route('d.drivers.single',$this->userId),true);
    }


    protected function getData($user)
    {
        $this->name = $user->name;
        $this->lastname = $user->lastname;
        $this->gender = $user->gender;
        $this->mobile = $user->mobile;
        $this->city = $user->city;
        $this->referralCode = $user->referral_code;
    }

    #[Computed]
    public function cities()
    {
        return City::all();
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

    public function redirectTo($route,$value = null)
    {
        $this->redirect(route($route,$value), true);

    }

}
