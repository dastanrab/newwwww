<?php

namespace App\Livewire\Dashboard\Users;

use App\Models\Car;
use App\Models\City;
use App\Models\Guild;
use App\Models\Role;
use App\Models\User;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;

class UserCreate extends Component
{
    public $roleId;
    public $password;
    public $gender;
    public $name;
    public $lastname;
    public $mobile;
    public $referral;
    public $city;
    public $userType;
    public $guildType;
    public $guildTitle;
    public $type;
    public $status;
    public $plaque1;
    public $plaque2;
    public $plaque3;
    public $plaque4;
    public $textButton = 'افزودن کاربر';
    public $breadCrumb = [['کاربران','d.users'],['افزودن کاربر','d.users.create']];

    #[Title('کاربران > افزودن کاربر')]

    public function mount()
    {
        $this->mobile = request()->submit ?? '';
    }

    #[Computed]
    public function guilds()
    {
        return Guild::all();
    }

    #[Computed]
    public function cities()
    {
        return City::all();
    }

    #[Computed]
    public function roles()
    {
        return Role::whereNotIn('name',['superadmin'])->get();
    }

    #[Computed]
    public function passwordRequiredIds()
    {
        return Role::passwordRequiredIds();
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

    public function render()
    {

        $this->authorize('user_create',User::class);
        return view('livewire.dashboard.users.user-create');
    }

    public function save($submit)
    {
        $is_driver=$this->roleId == 9 ? 'required' : 'nullable';
        $this->validate([
            'roleId' => 'required|exists:roles,id',
            'gender' => 'required|in:1,2',
            'name' => 'required|min:2',
            'lastname' => 'required|min:2',
            'mobile' => 'required|digits:11|numeric|unique:users,mobile',
            //'referral' => 'nullable|exists:users,mobile',
            'city' => 'exists:cities,id',
            'userType' => 'required|in:0,1',
            'guildType' => 'nullable|exists:guilds,id',
            'guildTitle' => $this->userType == 1 ? 'required' : 'nullable',
            'type' => $is_driver.'|in:1,2,3,4,5,6,7,8,9',
            'status' => $is_driver.'|in:active,deactive',
            'plaque1' => $is_driver.'|min:2|max:2',
            'plaque2' => $is_driver,
            'plaque3' => $is_driver.'|min:3|max:3',
            'plaque4' => $is_driver.'|min:2|max:2',
        ],[
            'roleId' => 'نقش کاربری درست را انتخاب کنید',
            'gender' => 'جنسیت را انتخاب کنید',
            'name' => 'نام را به درستی وارد نمایید',
            'lastname' => 'نام خانوادگی را به درستی وارد نمایید',
            'mobile.unique' => 'شماره همراه وارد شده در پایگاه داده وجود دارد',
            'mobile' => 'شماره همراه را به درستی وارد نمایید',
            //'referral.exists' => 'شماره همراه معرف وجود ندارد',
            'city.exists' => 'شهر را انتخاب نمایید',
            'userType' => 'نوع کاربر را انتخاب کنید',
            'guildTitle' => 'عنوان صنف را وارد نمایید',
            'type' => 'نوع خودرو را انتخاب نمایید',
            'status' => 'وضعیت را انتخاب کنید',
            'plaque1' => 'پلاک را به درستی وارد کنید',
            'plaque2' => 'پلاک را به درستی وارد کنید',
            'plaque3' => 'پلاک را به درستی وارد کنید',
            'plaque4' => 'پلاک را به درستی وارد کنید',
        ]);
        if(!empty($this->userType) && $this->userType == '1' && empty($this->guildType)){
            sendToast('0','لطفا نوع صنف را مشخص نمایید');
        }
        elseif(in_array($this->roleId,$this->passwordRequiredIds()) && strlen($this->password) < 8){
            sendToast('0','رمز عبور باید بیشتر ۷ کاراکتر باشد');
        }
        elseif ($this->referral && !User::find(User::getUserIdByReferral($this->referral))){
            sendToast('0','کد معرف اشتباه می باشد');
        }
        else {
            $user = User::register([
                'roleId' => $this->roleId,
                'gender' => $this->gender,
                'password' => $this->password,
                'name' => $this->name,
                'lastname' => $this->lastname,
                'mobile' => $this->mobile,
                'referral' => $this->referral,
                'cityId' => $this->city,
                'userType' => $this->userType,
                'guildId' => $this->userType ? $this->guildType : null,
                'guildTitle' => $this->guildTitle,
            ]);
            if ($this->roleId == 9)
            {
                Car::insert([
                    'driverId' => $user->id,
                    'type' => $this->type,
                    'isActive' => $this->status == 'active' ? 1 : 0,
                    'plaque1' => $this->plaque1,
                    'plaque2' => $this->plaque2,
                    'plaque3' => $this->plaque3,
                    'plaque4' => $this->plaque4,
                ]);
            }
            if($user) {
                sendToast(1, 'کاربر با موفقیت ایجاد شد');
                if(!empty($submit)){
                    redirect()->route('d.submits.tel',['userId' => $user->id]);
                }
                else{

                    redirect()->route('d.users.single',$user->id);
                }
            }
            else{
                sendToast(0, 'ایجاد کاربر با اشکال روبرو شد لطفا دوباره تلاش کنید');
            }
        }

    }

    #[On('test')]
    public function test($value){
        dd($value);
    }
}
