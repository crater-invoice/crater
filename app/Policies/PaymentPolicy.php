<?php

namespace Crater\Policies;

use Crater\Models\Payment;
use Crater\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Silber\Bouncer\BouncerFacade;

class PaymentPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \Crater\Models\User  $user
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
     * @param  \Crater\Models\User  $user
     * @param  \Crater\Models\Payment  $payment
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
     * @param  \Crater\Models\User  $user
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
     * @param  \Crater\Models\User  $user
     * @param  \Crater\Models\Payment  $payment
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
     * @param  \Crater\Models\User  $user
     * @param  \Crater\Models\Payment  $payment
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
     * @param  \Crater\Models\User  $user
     * @param  \Crater\Models\Payment  $payment
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
     * @param  \Crater\Models\User  $user
     * @param  \Crater\Models\Payment  $payment
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
     * @param  \Crater\Models\User  $user
     * @param  \Crater\Models\Payment  $payment
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
     * @param  \Crater\Models\User  $user
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
