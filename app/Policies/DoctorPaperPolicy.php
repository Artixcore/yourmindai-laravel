<?php

namespace App\Policies;

use App\Models\User;
use App\Models\DoctorPaper;

class DoctorPaperPolicy
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
    public function view(User $user, DoctorPaper $paper): bool
    {
        // Admin can view any paper, doctor can only view own papers
        return $user->role === 'admin' || $paper->user_id === $user->id;
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
    public function update(User $user, DoctorPaper $paper): bool
    {
        // Admin can update any paper, doctor can only update own papers
        return $user->role === 'admin' || $paper->user_id === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, DoctorPaper $paper): bool
    {
        // Admin can delete any paper, doctor can only delete own papers
        return $user->role === 'admin' || $paper->user_id === $user->id;
    }
}
