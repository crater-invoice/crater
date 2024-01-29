<?php

namespace InvoiceShelf\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use InvoiceShelf\Models\Company;
use InvoiceShelf\Models\User;
use Silber\Bouncer\BouncerFacade;

class DashboardPolicy
{
    use HandlesAuthorization;

    public function view(User $user, Company $company)
    {
        if (BouncerFacade::can('dashboard') && $user->hasCompany($company->id)) {
            return true;
        }

        return false;
    }
}
