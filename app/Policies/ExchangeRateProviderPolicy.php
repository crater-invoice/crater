<?php

namespace InvoiceShelf\Policies;

use InvoiceShelf\Models\ExchangeRateProvider;
use InvoiceShelf\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Silber\Bouncer\BouncerFacade;

class ExchangeRateProviderPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \InvoiceShelf\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        if (BouncerFacade::can('view-exchange-rate-provider', ExchangeRateProvider::class)) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \InvoiceShelf\Models\User  $user
     * @param  \InvoiceShelf\Models\ExchangeRateProvider  $exchangeRateProvider
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, ExchangeRateProvider $exchangeRateProvider)
    {
        if (BouncerFacade::can('view-exchange-rate-provider', $exchangeRateProvider) && $user->hasCompany($exchangeRateProvider->company_id)) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \InvoiceShelf\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        if (BouncerFacade::can('create-exchange-rate-provider', ExchangeRateProvider::class)) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \InvoiceShelf\Models\User  $user
     * @param  \InvoiceShelf\Models\ExchangeRateProvider  $exchangeRateProvider
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, ExchangeRateProvider $exchangeRateProvider)
    {
        if (BouncerFacade::can('edit-exchange-rate-provider', $exchangeRateProvider) && $user->hasCompany($exchangeRateProvider->company_id)) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \InvoiceShelf\Models\User  $user
     * @param  \InvoiceShelf\Models\ExchangeRateProvider  $exchangeRateProvider
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, ExchangeRateProvider $exchangeRateProvider)
    {
        if (BouncerFacade::can('delete-exchange-rate-provider', $exchangeRateProvider) && $user->hasCompany($exchangeRateProvider->company_id)) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \InvoiceShelf\Models\User  $user
     * @param  \InvoiceShelf\Models\ExchangeRateProvider  $exchangeRateProvider
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, ExchangeRateProvider $exchangeRateProvider)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \InvoiceShelf\Models\User  $user
     * @param  \InvoiceShelf\Models\ExchangeRateProvider  $exchangeRateProvider
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, ExchangeRateProvider $exchangeRateProvider)
    {
        //
    }
}
