<?php

namespace App\Livewire\Dashboard\Settings;

use App\Models\Role;
use Livewire\Component;

class RolesIndexList extends Component
{
    public $roles;

    public function mount()
    {
        $this->roles = Role::where('name','!=','superadmin')->get();
    }

    public function render()
    {
        return view('livewire.dashboard.settings.roles-index-list');
    }
}
