<?php

namespace InvoiceShelf\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use InvoiceShelf\Models\Item;
use InvoiceShelf\Models\Unit;
use InvoiceShelf\Models\User;
use Silber\Bouncer\BouncerFacade;

class UnitPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @return mixed
     */
    public function viewAny(User $user)
    {
        if (BouncerFacade::can('view-item', Item::class)) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @return mixed
     */
    public function view(User $user, Unit $unit)
    {
        if (BouncerFacade::can('view-item', Item::class) && $user->hasCompany($unit->company_id)) {
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
        if (BouncerFacade::can('view-item', Item::class)) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @return mixed
     */
    public function update(User $user, Unit $unit)
    {
        if (BouncerFacade::can('view-item', Item::class) && $user->hasCompany($unit->company_id)) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @return mixed
     */
    public function delete(User $user, Unit $unit)
    {
        if (BouncerFacade::can('view-item', Item::class) && $user->hasCompany($unit->company_id)) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @return mixed
     */
    public function restore(User $user, Unit $unit)
    {
        if (BouncerFacade::can('view-item', Item::class) && $user->hasCompany($unit->company_id)) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @return mixed
     */
    public function forceDelete(User $user, Unit $unit)
    {
        if (BouncerFacade::can('view-item', Item::class) && $user->hasCompany($unit->company_id)) {
            return true;
        }

        return false;
    }
}
