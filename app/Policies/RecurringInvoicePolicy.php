<?php

namespace InvoiceShelf\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use InvoiceShelf\Models\RecurringInvoice;
use InvoiceShelf\Models\User;
use Silber\Bouncer\BouncerFacade;

class RecurringInvoicePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        if (BouncerFacade::can('view-recurring-invoice', RecurringInvoice::class)) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, RecurringInvoice $recurringInvoice)
    {
        if (BouncerFacade::can('view-recurring-invoice', $recurringInvoice) && $user->hasCompany($recurringInvoice->company_id)) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        if (BouncerFacade::can('create-recurring-invoice', RecurringInvoice::class)) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, RecurringInvoice $recurringInvoice)
    {
        if (BouncerFacade::can('edit-recurring-invoice', $recurringInvoice) && $user->hasCompany($recurringInvoice->company_id)) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, RecurringInvoice $recurringInvoice)
    {
        if (BouncerFacade::can('delete-recurring-invoice', $recurringInvoice) && $user->hasCompany($recurringInvoice->company_id)) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, RecurringInvoice $recurringInvoice)
    {
        if (BouncerFacade::can('delete-recurring-invoice', $recurringInvoice) && $user->hasCompany($recurringInvoice->company_id)) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, RecurringInvoice $recurringInvoice)
    {
        if (BouncerFacade::can('delete-recurring-invoice', $recurringInvoice) && $user->hasCompany($recurringInvoice->company_id)) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete models.
     *
     * @return mixed
     */
    public function deleteMultiple(User $user)
    {
        if (BouncerFacade::can('delete-recurring-invoice', RecurringInvoice::class)) {
            return true;
        }

        return false;
    }
}
