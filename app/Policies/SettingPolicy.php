<?php

namespace App\Policies;

use App\Models\User;

class SettingPolicy
{

    public function setting_submit_time_index(User $user): bool
    {
        return $user->getPermissions()->contains('setting_submit_time_index');
    }

    public function setting_submit_time_edit(User $user): bool
    {
        return $user->getPermissions()->contains('setting_submit_time_edit');
    }

    public function setting_recyclable_index(User $user): bool
    {
        return $user->getPermissions()->contains('setting_recyclable_index');
    }

    public function setting_recyclable_single(User $user): bool
    {
        return $user->getPermissions()->contains('setting_recyclable_single');
    }

    public function setting_recyclable_single_edit(User $user): bool
    {
        return $user->getPermissions()->contains('setting_recyclable_single_edit');
    }

    public function setting_area_index(User $user): bool
    {
        return $user->getPermissions()->contains('setting_area_index');
    }
    public function setting_instant_index(User $user): bool
    {
        return $user->getPermissions()->contains('setting_instant_index');
    }

    public function setting_area_edit(User $user): bool
    {
        dd($user,'sss');
        return $user->getPermissions()->contains('setting_area_edit');
    }

    public function setting_role_index(User $user): bool
    {
        return $user->getPermissions()->contains('setting_role_index');
    }

    public function setting_role_single(User $user): bool
    {
        return $user->getPermissions()->contains('setting_role_single');
    }

    public function setting_role_single_edit(User $user): bool
    {
        return $user->getPermissions()->contains('setting_role_single_edit');
    }

    public function setting_general_index(User $user): bool
    {
        return $user->getPermissions()->contains('setting_general_index');
    }

    public function setting_map_index(User $user): bool
    {
        return $user->getPermissions()->contains('setting_map_index');
    }

}
