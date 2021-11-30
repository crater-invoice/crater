<?php

namespace Crater\Policies;

use Crater\Models\Expense;
use Crater\Models\ExpenseCategory;
use Crater\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Silber\Bouncer\BouncerFacade;

class ExpenseCategoryPolicy
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
        if (BouncerFacade::can('view-expense', Expense::class)) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \Crater\Models\User  $user
     * @param  \Crater\Models\ExpenseCategory  $expenseCategory
     * @return mixed
     */
    public function view(User $user, ExpenseCategory $expenseCategory)
    {
        if (BouncerFacade::can('view-expense', Expense::class) && $user->hasCompany($expenseCategory->company_id)) {
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
        if (BouncerFacade::can('view-expense', Expense::class)) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \Crater\Models\User  $user
     * @param  \Crater\Models\ExpenseCategory  $expenseCategory
     * @return mixed
     */
    public function update(User $user, ExpenseCategory $expenseCategory)
    {
        if (BouncerFacade::can('view-expense', Expense::class) && $user->hasCompany($expenseCategory->company_id)) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \Crater\Models\User  $user
     * @param  \Crater\Models\ExpenseCategory  $expenseCategory
     * @return mixed
     */
    public function delete(User $user, ExpenseCategory $expenseCategory)
    {
        if (BouncerFacade::can('view-expense', Expense::class) && $user->hasCompany($expenseCategory->company_id)) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \Crater\Models\User  $user
     * @param  \Crater\Models\ExpenseCategory  $expenseCategory
     * @return mixed
     */
    public function restore(User $user, ExpenseCategory $expenseCategory)
    {
        if (BouncerFacade::can('view-expense', Expense::class) && $user->hasCompany($expenseCategory->company_id)) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \Crater\Models\User  $user
     * @param  \Crater\Models\ExpenseCategory  $expenseCategory
     * @return mixed
     */
    public function forceDelete(User $user, ExpenseCategory $expenseCategory)
    {
        if (BouncerFacade::can('view-expense', Expense::class) && $user->hasCompany($expenseCategory->company_id)) {
            return true;
        }

        return false;
    }
}
