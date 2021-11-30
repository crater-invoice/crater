<?php

namespace Crater\Policies;

use Crater\Models\Item;
use Crater\Models\Unit;
use Crater\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Silber\Bouncer\BouncerFacade;

class UnitPolicy
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
        if (BouncerFacade::can('view-item', Item::class)) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \Crater\Models\User  $user
     * @param  \Crater\Models\Unit  $unit
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
     * @param  \Crater\Models\User  $user
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
     * @param  \Crater\Models\User  $user
     * @param  \Crater\Models\Unit  $unit
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
     * @param  \Crater\Models\User  $user
     * @param  \Crater\Models\Unit  $unit
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
     * @param  \Crater\Models\User  $user
     * @param  \Crater\Models\Unit  $unit
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
     * @param  \Crater\Models\User  $user
     * @param  \Crater\Models\Unit  $unit
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
