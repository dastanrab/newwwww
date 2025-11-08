<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    public function user_driver_index(User $user): bool
    {
        return $user->getPermissions()->contains('user_driver_index');
    }

    public function user_driver_create(User $user): bool
    {
        return $user->getPermissions()->contains('user_driver_create');
    }

    public function user_driver_single(User $user): bool
    {
        return $user->getPermissions()->contains('user_driver_single');
    }

    public function user_driver_index_rollcall(User $user): bool
    {
        return $user->getPermissions()->contains('user_driver_index_rollcall');
    }

    public function user_driver_index_rollcall_edit(User $user): bool
    {
        return $user->getPermissions()->contains('user_driver_index_rollcall_edit');
    }

    public function user_driver_single_edit(User $user): bool
    {
        return $user->getPermissions()->contains('user_driver_single_edit');
    }

    public function user_index(User $user): bool
    {
        return $user->getPermissions()->contains('user_index');
    }

    public function user_single(User $user): bool
    {
        return $user->getPermissions()->contains('user_single');
    }

    public function user_single_edit(User $user): bool
    {
        return $user->getPermissions()->contains('user_single_edit');
    }

    public function user_create(User $user): bool
    {
        return $user->getPermissions()->contains('user_create');
    }

    public function supervisor_driver_index(User $user): bool
    {
        return $user->getPermissions()->contains('supervisor_driver_index');
    }

    public function supervisor_driver_single(User $user): bool
    {
        return $user->getPermissions()->contains('supervisor_driver_single');
    }
    public function user_driver_index_mobile(User $user): bool
    {
        return $user->getPermissions()->contains('user_driver_index_mobile');
    }
    public function user_driver_index_car(User $user): bool
    {
        return $user->getPermissions()->contains('user_driver_index_car');
    }

}
