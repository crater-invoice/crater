<?php

namespace Crater\Policies;

use Crater\Models\MailSender;
use Crater\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Silber\Bouncer\BouncerFacade;

class MailSenderPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \Crater\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        if (BouncerFacade::can('view-mail-sender', MailSender::class)) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \Crater\Models\User  $user
     * @param  \Crater\Models\MailSender  $mailSender
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, MailSender $mailSender)
    {
        if (BouncerFacade::can('view-mail-sender', $mailSender) && $user->hasCompany($mailSender->company_id)) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \Crater\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        if (BouncerFacade::can('create-mail-sender', MailSender::class)) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \Crater\Models\User  $user
     * @param  \Crater\Models\MailSender  $mailSender
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, MailSender $mailSender)
    {
        if (BouncerFacade::can('edit-mail-sender', $mailSender) && $user->hasCompany($mailSender->company_id)) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \Crater\Models\User  $user
     * @param  \Crater\Models\MailSender  $mailSender
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, MailSender $mailSender)
    {
        if (BouncerFacade::can('delete-mail-sender', $mailSender) && $user->hasCompany($mailSender->company_id)) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \Crater\Models\User  $user
     * @param  \Crater\Models\MailSender  $mailSender
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, MailSender $mailSender)
    {
        if (BouncerFacade::can('delete-mail-sender', $mailSender) && $user->hasCompany($mailSender->company_id)) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \Crater\Models\User  $user
     * @param  \Crater\Models\MailSender  $mailSender
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, MailSender $mailSender)
    {
        if (BouncerFacade::can('delete-mail-sender', $mailSender) && $user->hasCompany($mailSender->company_id)) {
            return true;
        }

        return false;
    }
}
