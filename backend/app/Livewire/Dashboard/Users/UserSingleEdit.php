<?php

namespace App\Livewire\Dashboard\Users;

use App\Models\City;
use App\Models\Guild;
use App\Models\Role;
use App\Models\User;
use Livewire\Attributes\Computed;
use Livewire\Component;

class UserSingleEdit extends Component
{
    public User $user;
    public $roleId;
    public $password;
    public $gender;
    public $level;
    public $name;
    public $lastname;
    public $mobile;
    public $userType;
    public $guildType;
    public $guildTitle;
    public $referral;
    public $textButton = 'ویرایش کاربر';
    public $is_admin;
    public $operator;
    public $is_manager;

    public function boot()
    {
        $this->userType = $this->user->legal;
    }

    #[Computed]
    public function genders()
    {
        return User::genders();
    }

    #[Computed]
    public function levels()
    {
        return User::levels();
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
        $user = auth()->user();
        $roles = [];
        if(in_array($user->getRoles(0),['superadmin','admin'])){
            $roles = Role::whereNotIn('name',['superadmin'])->get();
        }
        return $roles;
    }

    #[Computed]
    public function presenter()
    {
        return $this->user->referral_code ? User::find(User::getUserIdByReferral($this->user->referral_code)) : '';
    }

    #[Computed]
    public function passwordRequiredIds()
    {
        return Role::passwordRequiredIds();
    }

    public function render()
    {
        $roles=auth()->user()->getRoles();
        $this->is_admin=$roles->contains( 'superadmin') or $roles->contains( 'admin');
        $this->operator=$roles->contains( 'operator');
        $this->is_manager=$roles->contains( 'manager');
        return view('livewire.dashboard.users.user-single-edit');
    }

    public function mount()
    {
         $this->roleId = $this->user->getRole('id');
         $this->gender = $this->user->gender;
         $this->level = $this->user->level;
         $this->name = $this->user->name;
         $this->lastname = $this->user->lastname;
         $this->guildType = $this->user->guild_id;
         $this->guildTitle = $this->user->guild_title;
         $this->referral = $this->user->referral_code;
    }

    public function update(){
        $this->validate([
            'roleId' => in_array(auth()->user()->getRoles(0),['admin,superadmin']) ? 'required|exists:roles,id' : 'nullable',
            'gender' => 'required|in:1,2',
            'name' => 'required|min:2',
            'lastname' => 'required|min:2',
            'userType' => 'required',
            'level' => 'required',
            //'referral' => $this->user->referral_code === null ? 'nullable|exists:users,mobile' : 'nullable',
            'guildType' => $this->user->guild_id === null ? 'nullable' : 'required|exists:guilds,id',
            'guildTitle' => $this->user->userType == 1 ? 'required' : 'nullable'
        ],[
            'roleId' => 'نقش کاربری درست را انتخاب کنید',
            'gender' => 'جنسیت را انتخاب کنید',
            'name' => 'نام را به درستی وارد نمایید',
            'lastname' => 'نام خانوادگی را به درستی وارد نمایید',
            'userType' => 'نوع کاربر را وارد نمایید',
            'level' => 'سطح کاربر را وارد نمایید',
            'mobile.unique' => 'شماره همراه وارد شده در پایگاه داده وجود دارد',
            'mobile' => 'شماره همراه را به درستی وارد نمایید',
            //'referral.exists' => 'شماره همراه معرف وجود ندارد',
            'city.exists' => 'شهر را انتخاب نمایید',
            'guildType' => 'نوع صنف را مشخص نمایید.',
            'guildTitle' => 'عنوان صنف را وارد نمایید',
        ]);
        if(!empty($this->password) && in_array($this->roleId,$this->passwordRequiredIds()) && strlen($this->password) < 8){
            sendToast(0,'رمز عبور باید بیشتر ۷ کاراکتر باشد');
        }
        elseif ($this->referral && !User::find(User::getUserIdByReferral($this->referral))){
            sendToast('0','کد معرف اشتباه می باشد');
        }
        else {
            $update = $this->user->profileUpdate([
                'roleId'     => $this->roleId,
                'password'   => $this->password,
                'gender'     => $this->gender,
                'level'     => $this->level,
                'name'       => $this->name,
                'lastname'   => $this->lastname,
                'referral'   => $this->referral,
                'guildTitle' => $this->guildTitle,
                'guildId'    => $this->guildType,
                'userType'   => $this->userType,
            ]);
            if($update){
                sendToast(1,'با موفقیت ویرایش شد');
            }

        }
    }
}
