<?php

namespace App\Policies;

use App\Models\PatientResource;
use App\Models\User;

class PatientResourcePolicy
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
    public function view(User $user, PatientResource $resource): bool
    {
        // Admin can view any resource, doctor can only view resources for their patients
        return $user->role === 'admin' || $resource->patient->doctor_id === $user->id;
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
    public function update(User $user, PatientResource $resource): bool
    {
        // Admin can update any resource, doctor can only update resources for their patients
        return $user->role === 'admin' || $resource->doctor_id === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, PatientResource $resource): bool
    {
        // Admin can delete any resource, doctor can only delete resources for their patients
        return $user->role === 'admin' || $resource->doctor_id === $user->id;
    }
}
