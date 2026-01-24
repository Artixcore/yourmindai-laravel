<?php

namespace App\Policies;

use App\Models\Feedback;
use App\Models\User;

class FeedbackPolicy
{
    /**
     * Determine if the user can view any feedback.
     */
    public function viewAny(User $user): bool
    {
        // Admin can view all feedback
        if ($user->role === 'admin') {
            return true;
        }

        // Doctors can view feedback for their patients
        return $user->role === 'doctor';
    }

    /**
     * Determine if the user can view the feedback.
     */
    public function view(User $user, Feedback $feedback): bool
    {
        // Admin can view all
        if ($user->role === 'admin') {
            return true;
        }

        // Doctor can view if they have access to the patient
        if ($user->role === 'doctor') {
            $patient = $feedback->patient;
            return $patient->doctor_id === $user->id || 
                   $user->assistedDoctors()->where('doctor_id', $patient->doctor_id)->exists();
        }

        return false;
    }

    /**
     * Determine if the user can delete the feedback.
     */
    public function delete(User $user, Feedback $feedback): bool
    {
        // Only admin can delete feedback
        return $user->role === 'admin';
    }
}
