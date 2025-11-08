<?php

namespace App\Policies;

use App\Models\User;

class StatPolicy
{
    public function stat_submit_index(User $user): bool
    {
        return $user->getPermissions()->contains('stat_submit_index');
    }

    public function stat_daily_index(User $user): bool
    {
        return $user->getPermissions()->contains('stat_daily_index');
    }

    public function stat_warehouse_driver_index(User $user): bool
    {
        return $user->getPermissions()->contains('stat_warehouse_driver_index') || in_array(auth()->id(),$this->allowed_ids());
    }

    public function stat_warehouse_driver_index_create(User $user): bool
    {
        return $user->getPermissions()->contains('stat_warehouse_driver_index_create');
    }

    public function stat_waste_index(User $user): bool
    {
        return $user->getPermissions()->contains('stat_waste_index');
    }

    public function stat_total_cost_index(User $user): bool
    {
        return $user->getPermissions()->contains('stat_total_cost_index');
    }

    public function stat_user_index(User $user): bool
    {
        return $user->getPermissions()->contains('stat_user_index');
    }

    public function stat_total_user_index(User $user): bool
    {
        return $user->getPermissions()->contains('stat_total_user_index');
    }

    public function stat_warehouse_index(User $user): bool
    {
        return $user->getPermissions()->contains('stat_warehouse_index');
    }

    public function stat_warehouse_create(User $user): bool
    {
        return $user->getPermissions()->contains('stat_warehouse_create');
    }

    public function stat_warehouse_daily_create(User $user): bool
    {
        return $user->getPermissions()->contains('stat_warehouse_daily_create');
    }

    public function stat_total_index(User $user): bool
    {
        return $user->getPermissions()->contains('stat_total_index');
    }

    public function stat_monthly_index(User $user): bool
    {
        return $user->getPermissions()->contains('stat_monthly_index');
    }

    public function stat_area_index(User $user): bool
    {
        return $user->getPermissions()->contains('stat_area_index');
    }

    public function stat_distance_index(User $user): bool
    {
        return $user->getPermissions()->contains('stat_distance_index');
    }
    public function stat_daily_index_full(User $user): bool
    {
        return $user->getPermissions()->contains('stat_daily_index_full');
    }
    public function stat_submit_division_excel(User $user): bool
    {
        return $user->getPermissions()->contains('stat_submit_division_excel');
    }
    public function stat_other_index(User $user): bool
    {
        return $user->getPermissions()->contains('stat_other_index');
    }

    private function allowed_ids()
    {
        return [72675];
    }
}
