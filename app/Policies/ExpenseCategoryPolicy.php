<?php

namespace InvoiceShelf\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use InvoiceShelf\Models\Expense;
use InvoiceShelf\Models\ExpenseCategory;
use InvoiceShelf\Models\User;
use Silber\Bouncer\BouncerFacade;

class ExpenseCategoryPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
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
