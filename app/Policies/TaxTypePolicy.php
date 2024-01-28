<?php

namespace InvoiceShelf\Policies;

use InvoiceShelf\Models\TaxType;
use InvoiceShelf\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Silber\Bouncer\BouncerFacade;

class TaxTypePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \InvoiceShelf\Models\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        if (BouncerFacade::can('view-tax-type', TaxType::class)) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \InvoiceShelf\Models\User  $user
     * @param  \InvoiceShelf\Models\TaxType  $taxType
     * @return mixed
     */
    public function view(User $user, TaxType $taxType)
    {
        if (BouncerFacade::can('view-tax-type', $taxType) && $user->hasCompany($taxType->company_id)) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \InvoiceShelf\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        if (BouncerFacade::can('create-tax-type', TaxType::class)) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \InvoiceShelf\Models\User  $user
     * @param  \InvoiceShelf\Models\TaxType  $taxType
     * @return mixed
     */
    public function update(User $user, TaxType $taxType)
    {
        if (BouncerFacade::can('edit-tax-type', $taxType) && $user->hasCompany($taxType->company_id)) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \InvoiceShelf\Models\User  $user
     * @param  \InvoiceShelf\Models\TaxType  $taxType
     * @return mixed
     */
    public function delete(User $user, TaxType $taxType)
    {
        if (BouncerFacade::can('delete-tax-type', $taxType) && $user->hasCompany($taxType->company_id)) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \InvoiceShelf\Models\User  $user
     * @param  \InvoiceShelf\Models\TaxType  $taxType
     * @return mixed
     */
    public function restore(User $user, TaxType $taxType)
    {
        if (BouncerFacade::can('delete-tax-type', $taxType) && $user->hasCompany($taxType->company_id)) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \InvoiceShelf\Models\User  $user
     * @param  \InvoiceShelf\Models\TaxType  $taxType
     * @return mixed
     */
    public function forceDelete(User $user, TaxType $taxType)
    {
        if (BouncerFacade::can('delete-tax-type', $taxType) && $user->hasCompany($taxType->company_id)) {
            return true;
        }

        return false;
    }
}
