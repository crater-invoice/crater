<?php

namespace Crater\Policies;

use Crater\Models\Company;
use Crater\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CompanyPolicy
{
    use HandlesAuthorization;

    public function create(User $user)
    {
        if ($user->isOwner()) {
            return true;
        }

        return false;
    }

    public function delete(User $user, Company $company)
    {
        if ($user->id == $company->owner_id) {
            return true;
        }

        return false;
    }

    public function transferOwnership(User $user, Company $company)
    {
        if ($user->id == $company->owner_id) {
            return true;
        }

        return false;
    }
}
