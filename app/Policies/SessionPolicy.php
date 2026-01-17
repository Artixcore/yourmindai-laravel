<?php

namespace App\Policies;

use App\Models\Session;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class SessionPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['admin', 'doctor']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Session $session): bool
    {
        // Admin can view any session, doctor can only view sessions for their patients
        if ($user->role === 'admin') {
            return true;
        }
        
        if ($user->role === 'doctor') {
            return $session->patient->doctor_id === $user->id;
        }
        
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return in_array($user->role, ['admin', 'doctor']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Session $session): bool
    {
        // Admin can update any session, doctor can only update sessions for their patients
        if ($user->role === 'admin') {
            return true;
        }
        
        if ($user->role === 'doctor') {
            return $session->patient->doctor_id === $user->id;
        }
        
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Session $session): bool
    {
        // Admin can delete any session, doctor can only delete sessions for their patients
        if ($user->role === 'admin') {
            return true;
        }
        
        if ($user->role === 'doctor') {
            return $session->patient->doctor_id === $user->id;
        }
        
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Session $session): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Session $session): bool
    {
        return false;
    }
}
