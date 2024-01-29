<?php

namespace InvoiceShelf\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use InvoiceShelf\Models\CustomField;
use InvoiceShelf\Models\User;
use Silber\Bouncer\BouncerFacade;

class CustomFieldPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @return mixed
     */
    public function viewAny(User $user)
    {
        if (BouncerFacade::can('view-custom-field', CustomField::class)) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @return mixed
     */
    public function view(User $user, CustomField $customField)
    {
        if (BouncerFacade::can('view-custom-field', $customField) && $user->hasCompany($customField->company_id)) {
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
        if (BouncerFacade::can('create-custom-field', CustomField::class)) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @return mixed
     */
    public function update(User $user, CustomField $customField)
    {
        if (BouncerFacade::can('edit-custom-field', $customField) && $user->hasCompany($customField->company_id)) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @return mixed
     */
    public function delete(User $user, CustomField $customField)
    {
        if (BouncerFacade::can('delete-custom-field', $customField) && $user->hasCompany($customField->company_id)) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @return mixed
     */
    public function restore(User $user, CustomField $customField)
    {
        if (BouncerFacade::can('delete-custom-field', $customField) && $user->hasCompany($customField->company_id)) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @return mixed
     */
    public function forceDelete(User $user, CustomField $customField)
    {
        if (BouncerFacade::can('delete-custom-field', $customField) && $user->hasCompany($customField->company_id)) {
            return true;
        }

        return false;
    }
}
