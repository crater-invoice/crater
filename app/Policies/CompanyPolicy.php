<?php

namespace InvoiceShelf\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use InvoiceShelf\Models\Company;
use InvoiceShelf\Models\User;

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
