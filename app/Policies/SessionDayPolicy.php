<?php

namespace App\Policies;

use App\Models\SessionDay;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class SessionDayPolicy
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
    public function view(User $user, SessionDay $sessionDay): bool
    {
        // Admin can view any day, doctor can only view days for their sessions
        if ($user->role === 'admin') {
            return true;
        }
        
        if ($user->role === 'doctor') {
            return $sessionDay->session->patient->doctor_id === $user->id;
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
    public function update(User $user, SessionDay $sessionDay): bool
    {
        // Admin can update any day, doctor can only update days for their sessions
        if ($user->role === 'admin') {
            return true;
        }
        
        if ($user->role === 'doctor') {
            return $sessionDay->session->patient->doctor_id === $user->id;
        }
        
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, SessionDay $sessionDay): bool
    {
        // Admin can delete any day, doctor can only delete days for their sessions
        if ($user->role === 'admin') {
            return true;
        }
        
        if ($user->role === 'doctor') {
            return $sessionDay->session->patient->doctor_id === $user->id;
        }
        
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, SessionDay $sessionDay): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, SessionDay $sessionDay): bool
    {
        return false;
    }
}
