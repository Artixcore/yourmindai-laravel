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
        // Admin can view any resource
        if ($user->role === 'admin') {
            return true;
        }
        
        // Doctor can only view resources for their patients
        if ($user->role === 'doctor' && $resource->patient->doctor_id === $user->id) {
            return true;
        }
        
        // Patient (User with role 'PATIENT') can view their own resources
        if ($user->role === 'PATIENT') {
            // Check if resource's patient email matches user's email
            return $resource->patient && $resource->patient->email === $user->email;
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
