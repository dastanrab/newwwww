<?php

namespace App\Policies;

use App\Models\Club;
use App\Models\User;

class ClubPolicy
{
    public function view(User $user, Club $club): bool
    {
        return $user->id == $club->user_id;
    }

    public function club_index(User $user): bool
    {
        return $user->getPermissions()->contains('club_index');
    }

    public function club_create(User $user): bool
    {
        return $user->getPermissions()->contains('club_create');
    }

    public function club_edit(User $user): bool
    {
        return $user->getPermissions()->contains('club_edit');
    }

    public function club_category_index(User $user): bool
    {
        return $user->getPermissions()->contains('club_category_index');
    }

    public function club_category_create(User $user): bool
    {
        return $user->getPermissions()->contains('club_category_create');
    }


    public function club_category_edit(User $user): bool
    {
        return $user->getPermissions()->contains('club_category_edit');
    }

    public function club_offer_index(User $user): bool
    {
        return $user->getPermissions()->contains('club_offer_index');
    }

}
