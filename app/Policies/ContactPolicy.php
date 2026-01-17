<?php

namespace App\Policies;

use App\Models\ContactMessage;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ContactPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Admin and assistants can view contact messages
        return $user->isAdmin() || $user->isAssistant();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ContactMessage $contactMessage): bool
    {
        // Admin and assistants can view contact messages
        return $user->isAdmin() || $user->isAssistant();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Anyone can create contact messages (public form)
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ContactMessage $contactMessage): bool
    {
        // Only admin can update (resolve, add notes)
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ContactMessage $contactMessage): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ContactMessage $contactMessage): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ContactMessage $contactMessage): bool
    {
        return $user->isAdmin();
    }
}
