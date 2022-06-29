<?php

namespace Crater\Policies;

use Crater\Models\Group;
use Crater\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Silber\Bouncer\BouncerFacade;

class GroupPolicy
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
        if (BouncerFacade::can('view-group', Group::class)) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \Crater\Models\User  $user
     * @param  \Crater\Models\Group  $group
     * @return mixed
     */
    public function view(User $user, Group $group)
    {
        if (BouncerFacade::can('view-group', $group)) {
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
        if (BouncerFacade::can('create-group', Group::class)) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param \Crater\Models\Group $group
     * @return mixed
     */
    public function update(User $user, Group $group)
    {
        if (BouncerFacade::can('edit-group', $group)) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param \Crater\Models\Group $group
     * @return mixed
     */
    public function delete(User $user, Group $group)
    {
        if (BouncerFacade::can('delete-group', $group)) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param User $user
     * @param \Crater\Models\Group $group
     * @return mixed
     */
    public function restore(User $user, Group $group)
    {
        if (BouncerFacade::can('delete-group', $group)) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param User $user
     * @param Group $group
     * @return mixed
     */
    public function forceDelete(User $user, Group $group)
    {
        if (BouncerFacade::can('delete-group', $group)) {
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
        if (BouncerFacade::can('delete-group', Group::class)) {
            return true;
        }

        return false;
    }
}
