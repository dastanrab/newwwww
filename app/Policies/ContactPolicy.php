<?php

namespace App\Policies;

use App\Models\Contact;
use App\Models\Message;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ContactPolicy
{

    public function contact_index(User $user): bool
    {
        return $user->getPermissions()->contains('contact_index');
    }

    public function contact_single(User $user): bool
    {
        return $user->getPermissions()->contains('contact_single');
    }

    public function contact_single_edit(User $user): bool
    {
        return $user->getPermissions()->contains('contact_single_edit');
    }

    public function edit(User $user, Contact $contact): bool
    {
        return $contact->user_id == $user->id;
    }

}
