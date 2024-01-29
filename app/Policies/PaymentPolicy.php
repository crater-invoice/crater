<?php

namespace InvoiceShelf\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use InvoiceShelf\Models\Payment;
use InvoiceShelf\Models\User;
use Silber\Bouncer\BouncerFacade;

class PaymentPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @return mixed
     */
    public function viewAny(User $user)
    {
        if (BouncerFacade::can('view-payment', Payment::class)) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @return mixed
     */
    public function view(User $user, Payment $payment)
    {
        if (BouncerFacade::can('view-payment', $payment) && $user->hasCompany($payment->company_id)) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     *
     * @return mixed
     */
    public function create(User $user)
    {
        if (BouncerFacade::can('create-payment', Payment::class)) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @return mixed
     */
    public function update(User $user, Payment $payment)
    {
        if (BouncerFacade::can('edit-payment', $payment) && $user->hasCompany($payment->company_id)) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @return mixed
     */
    public function delete(User $user, Payment $payment)
    {
        if (BouncerFacade::can('delete-payment', $payment) && $user->hasCompany($payment->company_id)) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @return mixed
     */
    public function restore(User $user, Payment $payment)
    {
        if (BouncerFacade::can('delete-payment', $payment) && $user->hasCompany($payment->company_id)) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @return mixed
     */
    public function forceDelete(User $user, Payment $payment)
    {
        if (BouncerFacade::can('delete-payment', $payment) && $user->hasCompany($payment->company_id)) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can send email of the model.
     *
     * @return mixed
     */
    public function send(User $user, Payment $payment)
    {
        if (BouncerFacade::can('send-payment', $payment) && $user->hasCompany($payment->company_id)) {
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
        if (BouncerFacade::can('delete-payment', Payment::class)) {
            return true;
        }

        return false;
    }
}
