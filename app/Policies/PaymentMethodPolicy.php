<?php

namespace InvoiceShelf\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use InvoiceShelf\Models\Payment;
use InvoiceShelf\Models\PaymentMethod;
use InvoiceShelf\Models\User;
use Silber\Bouncer\BouncerFacade;

class PaymentMethodPolicy
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
    public function view(User $user, PaymentMethod $paymentMethod)
    {
        if (BouncerFacade::can('view-payment', Payment::class) && $user->hasCompany($paymentMethod->company_id)) {
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
        if (BouncerFacade::can('view-payment', Payment::class)) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @return mixed
     */
    public function update(User $user, PaymentMethod $paymentMethod)
    {
        if (BouncerFacade::can('view-payment', Payment::class) && $user->hasCompany($paymentMethod->company_id)) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @return mixed
     */
    public function delete(User $user, PaymentMethod $paymentMethod)
    {
        if (BouncerFacade::can('view-payment', Payment::class) && $user->hasCompany($paymentMethod->company_id)) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @return mixed
     */
    public function restore(User $user, PaymentMethod $paymentMethod)
    {
        if (BouncerFacade::can('view-payment', Payment::class) && $user->hasCompany($paymentMethod->company_id)) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @return mixed
     */
    public function forceDelete(User $user, PaymentMethod $paymentMethod)
    {
        if (BouncerFacade::can('view-payment', Payment::class) && $user->hasCompany($paymentMethod->company_id)) {
            return true;
        }

        return false;
    }
}
