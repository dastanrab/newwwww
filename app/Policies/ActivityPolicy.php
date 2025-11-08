<?php

namespace App\Policies;

use App\Models\Activity;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ActivityPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function activity_index(User $user): bool
    {
        return $user->getPermissions()->contains('activity_index');
    }

}
