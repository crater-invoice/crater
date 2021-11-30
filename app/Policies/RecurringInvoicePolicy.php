<?php

namespace Crater\Policies;

use Crater\Models\RecurringInvoice;
use Crater\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Silber\Bouncer\BouncerFacade;

class RecurringInvoicePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \Crater\Models\User  $user
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
     * @param  \Crater\Models\User  $user
     * @param  \Crater\Models\RecurringInvoice  $recurringInvoice
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
     * @param  \Crater\Models\User  $user
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
     * @param  \Crater\Models\User  $user
     * @param  \Crater\Models\RecurringInvoice  $recurringInvoice
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
     * @param  \Crater\Models\User  $user
     * @param  \Crater\Models\RecurringInvoice  $recurringInvoice
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
     * @param  \Crater\Models\User  $user
     * @param  \Crater\Models\RecurringInvoice  $recurringInvoice
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
     * @param  \Crater\Models\User  $user
     * @param  \Crater\Models\RecurringInvoice  $recurringInvoice
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
     * @param  \Crater\Models\User  $user
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
