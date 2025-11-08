<?php

namespace App\Policies;

use App\Models\User;

class WalletPolicy
{
    public function wallet_all_index(User $user): bool
    {
        return $user->getPermissions()->contains('wallet_all_index');
    }

    public function wallet_all_index_withdraw(User $user): bool
    {
        return $user->getPermissions()->contains('wallet_all_index_withdraw');
    }

    public function wallet_all_deposit_bazist_wallet(User $user): bool
    {
        return $user->getPermissions()->contains('wallet_all_deposit_bazist_wallet');
    }

    public function wallet_all_withdraw_bazist_wallet(User $user): bool
    {
        return $user->getPermissions()->contains('wallet_all_withdraw_bazist_wallet');
    }

    public function wallet_all_index_add_card(User $user): bool
    {
        return $user->getPermissions()->contains('wallet_all_index_add_card');
    }

    public function wallet_all_single(User $user): bool
    {
        return $user->getPermissions()->contains('wallet_all_single');
    }

    public function cashout_all_index(User $user): bool
    {
        return $user->getPermissions()->contains('cashout_all_index');
    }

    public function cashout_all_single(User $user): bool
    {
        return $user->getPermissions()->contains('cashout_all_single');
    }

    public function cashout_all_send_to_bank(User $user): bool
    {
        return $user->getPermissions()->contains('cashout_all_send_to_bank');
    }

    public function cashout_all_back_to_wallet(User $user): bool
    {
        return $user->getPermissions()->contains('cashout_all_back_to_wallet');
    }

    public function wallet_ap_index(User $user): bool
    {
        return $user->getPermissions()->contains('wallet_ap_index');
    }

    public function wallet_ap_create(User $user): bool
    {
        return $user->getPermissions()->contains('wallet_ap_create');
    }

}
