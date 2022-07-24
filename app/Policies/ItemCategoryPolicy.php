<?php

namespace Crater\Policies;

use Crater\Models\ItemCategory;
use Crater\Models\Item;
use Crater\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Silber\Bouncer\BouncerFacade;

class ItemCategoryPolicy
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
     * @param  \Crater\Models\ItemCategory  $itemCategory
     * @return mixed
     */
    public function view(User $user, ItemCategory $itemCategory)
    {
        if (BouncerFacade::can('view-item', Item::class) && $user->hasCompany($itemCategory->company_id)) {
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
     * @param  \Crater\Models\ItemCategory  $itemCategory
     * @return mixed
     */
    public function update(User $user, ItemCategory $itemCategory)
    {
        if (BouncerFacade::can('view-item', Item::class) && $user->hasCompany($itemCategory->company_id)) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \Crater\Models\User  $user
     * @param  \Crater\Models\ItemCategory  $itemCategory
     * @return mixed
     */
    public function delete(User $user, ItemCategory $itemCategory)
    {
        if (BouncerFacade::can('view-item', Item::class) && $user->hasCompany($itemCategory->company_id)) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \Crater\Models\User  $user
     * @param  \Crater\Models\ItemCategory  $itemCategory
     * @return mixed
     */
    public function restore(User $user, ItemCategory $itemCategory)
    {
        if (BouncerFacade::can('view-item', Item::class) && $user->hasCompany($itemCategory->company_id)) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \Crater\Models\User  $user
     * @param  \Crater\Models\ItemCategory  $itemCategory
     * @return mixed
     */
    public function forceDelete(User $user, ItemCategory $itemCategory)
    {
        if (BouncerFacade::can('view-item', Item::class) && $user->hasCompany($itemCategory->company_id)) {
            return true;
        }

        return false;
    }
}
