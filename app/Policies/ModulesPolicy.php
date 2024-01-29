<?php

namespace InvoiceShelf\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use InvoiceShelf\Models\User;

class ModulesPolicy
{
    use HandlesAuthorization;

    public function manageModules(User $user)
    {
        if ($user->isOwner()) {
            return true;
        }

        return false;
    }
}
