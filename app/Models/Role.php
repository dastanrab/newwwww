<?php

namespace App\Models;

use App\RecordsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use RecordsActivity;
    use HasFactory;

    protected $fillable = [
        'name',
        'label',
    ];

    public function permissions(){
        return $this->belongsToMany(Permission::class);
    }

    public function users(){
        return $this->belongsToMany(User::class);
    }

    public function givePermissionTo(Permission $permission){
        return $this->permissions()->save($permission);
    }

    public static function getRoleId($role = '') {
        $roles = Role::all()->pluck('id','name');
        return $role ? $roles->get($role) : $roles;
    }

    public static function AccessToDashboard() {
        return collect(['superadmin', 'admin', 'supervisor',  'senior_supervisor', 'operator', 'accountants', 'marketer', 'warehouser','manager','senior_operator', 'financial_manager', 'analyst']);
    }

    public static function passwordRequiredIds()
    {
        $roles = Role::whereNotIn('name', ['user','driver'])->pluck('id')->toArray();
        return $roles;
    }
}
