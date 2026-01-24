<?php

namespace App\Policies;

use App\Models\PracticeProgression;
use App\Models\User;

class PracticeProgressionPolicy
{
    /**
     * Determine if the user can view any practice progressions.
     */
    public function viewAny(User $user): bool
    {
        // Admin can view all
        if ($user->role === 'admin') {
            return true;
        }

        // Doctors can view progressions for their patients
        return $user->role === 'doctor';
    }

    /**
     * Determine if the user can view the practice progression.
     */
    public function view(User $user, PracticeProgression $progression): bool
    {
        // Admin can view all
        if ($user->role === 'admin') {
            return true;
        }

        // Doctor can view if they have access to the patient
        if ($user->role === 'doctor') {
            $patient = $progression->patient;
            return $patient->doctor_id === $user->id || 
                   $user->assistedDoctors()->where('doctor_id', $patient->doctor_id)->exists();
        }

        return false;
    }
}
