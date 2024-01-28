<?php

namespace InvoiceShelf\Policies;

use InvoiceShelf\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

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
