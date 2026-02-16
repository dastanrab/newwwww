<?php

namespace App\Livewire\Dashboard\Users;

use App\Models\Role;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class UserIndexNav extends Component
{
    public $roles;
    #[Url]
    public $role;
    #[Url]
    public $level;
    public function render()
    {
        $this->roles = Role::whereNotIn('name', ['superadmin'])->get();
        return view('livewire.dashboard.users.user-index-nav');
    }

    public function filterRole($value)
    {
        $this->role = $value;
        $this->dispatch('role', role : $value);
    }
    public function filterLevel($value)
    {
        $this->level = $value;
        $this->dispatch('level', level : $value);
    }

}
