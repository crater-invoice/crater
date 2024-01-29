<?php

namespace InvoiceShelf\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use InvoiceShelf\Models\Note;
use InvoiceShelf\Models\User;
use Silber\Bouncer\BouncerFacade;

class NotePolicy
{
    use HandlesAuthorization;

    public function manageNotes(User $user)
    {
        if (BouncerFacade::can('manage-all-notes', Note::class)) {
            return true;
        }

        return false;
    }

    public function viewNotes(User $user)
    {
        if (BouncerFacade::can('view-all-notes', Note::class)) {
            return true;
        }

        return false;
    }
}
