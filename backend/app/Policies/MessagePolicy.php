<?php

namespace App\Policies;

use App\Models\Message;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class MessagePolicy
{
    public function notification_index(User $user): bool
    {
        return $user->getPermissions()->contains('notification_index');
    }
    public function notification_create(User $user): bool
    {
        return $user->getPermissions()->contains('notification_create');
    }

}
