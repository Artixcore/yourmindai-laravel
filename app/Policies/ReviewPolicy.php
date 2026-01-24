<?php

namespace App\Policies;

use App\Models\Review;
use App\Models\User;
use App\Models\Patient;
use Illuminate\Auth\Access\Response;

class ReviewPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Admins can view all reviews
        // Doctors can view their own reviews
        // Patients can view their own reviews
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Review $review): bool
    {
        // Admins can view any review
        if ($user->isAdmin()) {
            return true;
        }

        // Doctors can view reviews about them
        if ($user->isDoctor() && $review->doctor_id === $user->id) {
            return true;
        }

        // Patients can view their own reviews
        $patient = Patient::where('email', $user->email)->first();
        if ($patient && $review->patient_id === $patient->id) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Only patients who have sessions can create reviews
        $patient = Patient::where('email', $user->email)->first();
        
        if (!$patient) {
            return false;
        }

        // Check if patient has any sessions
        return $patient->sessions()->exists();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Review $review): bool
    {
        // Only the patient who created the review can update it
        $patient = Patient::where('email', $user->email)->first();
        
        if (!$patient || $review->patient_id !== $patient->id) {
            return false;
        }

        // Only allow updates within 48 hours of creation
        return $review->canBeEdited();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Review $review): bool
    {
        // Only admins can delete reviews
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Review $review): bool
    {
        // Only admins can restore reviews
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Review $review): bool
    {
        // Only admins can permanently delete reviews
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can moderate the review.
     */
    public function moderate(User $user, Review $review): bool
    {
        // Only admins can moderate reviews (flag/unflag)
        return $user->isAdmin();
    }
}
