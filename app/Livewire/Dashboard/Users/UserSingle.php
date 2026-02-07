<?php

namespace App\Livewire\Dashboard\Users;

use App\Models\City;
use App\Models\Guild;
use App\Models\Role;
use App\Models\User;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

class UserSingle extends Component
{
    public $breadCrumb = [['کاربران','d.users'], ['ویرایش کاربر','d.users.single']];
    #[Title('کاربران > ویرایش کاربر')]

    public User $user;


    public function render()
    {
        $this->authorize('user_single',User::class);
        return view('livewire.dashboard.users.user-single');
    }
}
