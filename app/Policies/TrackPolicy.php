<?php

namespace App\Policies;

use App\Models\User;

class TrackPolicy
{
    public function track_online_index(User $user): bool
    {
        return $user->getPermissions()->contains('track_online_index');
    }

    public function track_report_index(User $user): bool
    {
        return $user->getPermissions()->contains('track_report_index');
    }
}
