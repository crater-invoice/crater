<?php

namespace InvoiceShelf\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use InvoiceShelf\Models\Company;
use InvoiceShelf\Models\User;

class SettingsPolicy
{
    use HandlesAuthorization;

    public function manageCompany(User $user, Company $company)
    {
        if ($user->id == $company->owner_id) {
            return true;
        }

        return false;
    }

    public function manageBackups(User $user)
    {
        if ($user->isOwner()) {
            return true;
        }

        return false;
    }

    public function manageFileDisk(User $user)
    {
        if ($user->isOwner()) {
            return true;
        }

        return false;
    }

    public function manageEmailConfig(User $user)
    {
        if ($user->isOwner()) {
            return true;
        }

        return false;
    }

    public function manageSettings(User $user)
    {
        if ($user->isOwner()) {
            return true;
        }

        return false;
    }
}
