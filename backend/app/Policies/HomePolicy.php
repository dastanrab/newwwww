<?php

namespace App\Policies;

use App\Models\User;

class HomePolicy
{
    /**
     * Create a new policy instance.
     */
    public function home_index(User $user): bool
    {
        return $user->getPermissions()->contains('home_index');
    }
}
