<?php

namespace App\Livewire\Dashboard\Drivers;

use App\Models\Car;
use App\Models\City;
use App\Models\User;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

class DriverSingle extends Component
{
    public $breadCrumb = [['رانندگان','d.drivers'], ['ویرایش راننده','d.drivers.single']];
    #[Title('رانندگان > ویرایش راننده')]

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
        $this->authorize('user_driver_single',User::class);
        return view('livewire.dashboard.drivers.driver-single');
    }

    #[Computed]
    public function genders()
    {
        return User::genders();
    }

    #[Computed]
    public function cities()
    {
        return City::all();
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
        $this->type = $user->cars()->first()->type_id;
        $this->status = $user->cars()->first()->is_active ? 'active' : 'deactive';
        $this->plaque1 = $user->cars()->first()->plaque_1;
        $this->plaque2 = $user->cars()->first()->plaque_2;
        $this->plaque3 = $user->cars()->first()->plaque_3;
        $this->plaque4 = $user->cars()->first()->plaque_4;
    }

    public function update()
    {
        $this->validate([
            'type'    => 'required|in:1,2,3,4,5,6,7,8,9',
            'status'  => 'required|in:active,deactive',
            'plaque1' => 'required|min:2|max:2',
            'plaque2' => 'required',
            'plaque3' => 'required|min:3|max:3',
            'plaque4' => 'required|min:2|max:2',
        ],[
            'type'    => 'نوع خودرو را انتخاب نمایید',
            'status'  => 'وضعیت را انتخاب کنید',
            'plaque1' => 'پلاک را به درستی وارد کنید',
            'plaque2' => 'پلاک را به درستی وارد کنید',
            'plaque3' => 'پلاک را به درستی وارد کنید',
            'plaque4' => 'پلاک را به درستی وارد کنید',
        ]);
        $carType = Car::types()->pluck('label','name');
        if($this->status == 'active'){
            $data = [
                'plaque_1'  => $this->plaque1,
                'plaque_2'  => $this->plaque2,
                'plaque_3'  => $this->plaque3,
                'plaque_4'  => $this->plaque4,
                'plaque'    => "$this->plaque1/$this->plaque2/$this->plaque3/$this->plaque4",
                'type'      => $carType[$this->type],
                'type_id'   => $this->type,
                'is_active' => 1,
            ];
        }
        elseif ($this->status == 'deactive'){
            $data = [
                'plaque_1'        => $this->plaque1,
                'plaque_2'        => $this->plaque2,
                'plaque_3'        => $this->plaque3,
                'plaque_4'        => $this->plaque4,
                'plaque'          => "$this->plaque1/$this->plaque2/$this->plaque3/$this->plaque4",
                'type'            => $carType[$this->type],
                'type_id'         => $this->type,
                'is_active'       => 0,
                'rollcall_status' => 0,
            ];
        }

        $this->driver->car->update($data);
        return sendToast(1,'با موفقیت ویرایش شد');
    }

    public function redirectTo($route,$value = null)
    {
        $this->redirect(route($route,$value), true);

    }
}
