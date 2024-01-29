<?php

namespace InvoiceShelf\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use InvoiceShelf\Models\Company;
use InvoiceShelf\Models\User;
use Silber\Bouncer\BouncerFacade;

class ReportPolicy
{
    use HandlesAuthorization;

    public function viewReport(User $user, Company $company)
    {
        if (BouncerFacade::can('view-financial-reports') && $user->hasCompany($company->id)) {
            return true;
        }

        return false;
    }
}
