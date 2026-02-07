<?php

namespace App\Livewire\Dashboard\Settings;

use App\Models\Permission;
use App\Models\Role;
use Livewire\Attributes\Title;
use Livewire\Component;

class RolesSingle extends Component
{

    public $breadCrumb = [['سطح دسترسی','d.settings.roles'],['ویرایش','d.settings.roles.single']];
    #[Title('سطح دسترسی > ویرایش')]

    public Role $role;
    public $permissions;
    public $permissionSelected;

    public function mount()
    {
        $this->permissions = Permission::all();
        $this->permissionSelected = $this->role->permissions()->pluck('id');
    }

    public function render()
    {
        $this->authorize('setting_role_single',RolesIndex::class);
        return view('livewire.dashboard.settings.roles-single');
    }

    public function select()
    {
        if($this->permissionSelected){
            $this->permissionSelected = [];
        }
        else{
            $this->permissionSelected = Permission::pluck('id');
        }
    }

    public function update()
    {
        $values = $this->permissionSelected ? $this->permissionSelected->values() : [];
        $this->role->permissions()->sync($values);
        sendToast(1,'با موفقیت ویرایش شد');
    }
}
