<?php

namespace App\Policies;

use App\Models\Submit;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class SubmitPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function submit_tel_index(User $user): bool
    {
        return $user->getPermissions()->contains('submit_tel_index');
    }

    public function submit_tel_create_add_card(User $user): bool
    {
        return $user->getPermissions()->contains('submit_tel_create_add_card');
    }

    public function submit_tel_create_add_address(User $user): bool
    {
        return $user->getPermissions()->contains('submit_tel_create_add_address');
    }

    public function submit_all_index(User $user): bool
    {
        return $user->getPermissions()->contains('submit_all_index');
    }

    public function submit_all_index_list_change_driver(User $user): bool
    {
        return $user->getPermissions()->contains('submit_all_index_list_change_driver');
    }

    public function submit_all_index_list_cancel_submit(User $user): bool
    {
        return $user->getPermissions()->contains('submit_all_index_list_cancel_submit');
    }

    public function submit_map_index(User $user): bool
    {
        return $user->getPermissions()->contains('submit_map_index');
    }

    public function submit_map_index_detail(User $user): bool
    {
        return $user->getPermissions()->contains('submit_map_index_detail');
    }

    public function submit_delete_card(User $user): bool
    {
        return $user->getPermissions()->contains('submit_delete_card');
    }
    public function submit_delete_address(User $user): bool
    {
        return $user->getPermissions()->contains('submit_delete_address');
    }
}
