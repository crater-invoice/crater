<?php

namespace Crater\Policies;

use Crater\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

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
