<?php

namespace Crater\Policies;

use Crater\Models\Customer;
use Crater\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Silber\Bouncer\BouncerFacade;

class CustomerPolicy
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
        if (BouncerFacade::can('view-customer', Customer::class)) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \Crater\Models\User  $user
     * @param  \Crater\Models\Customer  $customer
     * @return mixed
     */
    public function view(User $user, Customer $customer)
    {
        if (BouncerFacade::can('view-customer', $customer)) {
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
        if (BouncerFacade::can('create-customer', Customer::class)) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \Crater\Models\User  $user
     * @param  \Crater\Models\Customer  $customer
     * @return mixed
     */
    public function update(User $user, Customer $customer)
    {
        if (BouncerFacade::can('edit-customer', $customer)) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \Crater\Models\User  $user
     * @param  \Crater\Models\Customer  $customer
     * @return mixed
     */
    public function delete(User $user, Customer $customer)
    {
        if (BouncerFacade::can('delete-customer', $customer)) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \Crater\Models\User  $user
     * @param  \Crater\Models\Customer  $customer
     * @return mixed
     */
    public function restore(User $user, Customer $customer)
    {
        if (BouncerFacade::can('delete-customer', $customer)) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \Crater\Models\User  $user
     * @param  \Crater\Models\Customer  $customer
     * @return mixed
     */
    public function forceDelete(User $user, Customer $customer)
    {
        if (BouncerFacade::can('delete-customer', $customer)) {
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
        if (BouncerFacade::can('delete-customer', Customer::class)) {
            return true;
        }

        return false;
    }
}
