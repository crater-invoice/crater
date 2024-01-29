<?php

namespace InvoiceShelf\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use InvoiceShelf\Models\User;

class OwnerPolicy
{
    use HandlesAuthorization;

    public function managedByOwner(User $user)
    {
        if ($user->isOwner()) {
            return true;
        }

        return false;
    }
}
