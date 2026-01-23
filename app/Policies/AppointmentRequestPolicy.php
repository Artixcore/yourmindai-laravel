<?php

namespace App\Policies;

use App\Models\User;
use App\Models\AppointmentRequest;

class AppointmentRequestPolicy
{
    /**
     * Determine if the user can view any appointment requests.
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['admin', 'doctor']);
    }

    /**
     * Determine if the user can view the appointment request.
     */
    public function view(User $user, AppointmentRequest $appointmentRequest): bool
    {
        // Admin can view all requests
        if ($user->role === 'admin') {
            return true;
        }
        
        // Doctor can view requests assigned to them
        if ($user->role === 'doctor') {
            return $appointmentRequest->doctor_id === $user->id;
        }
        
        return false;
    }

    /**
     * Determine if the user can create appointment requests.
     */
    public function create(User $user): bool
    {
        // Public users can create (no auth required)
        return true;
    }

    /**
     * Determine if the user can update the appointment request.
     */
    public function update(User $user, AppointmentRequest $appointmentRequest): bool
    {
        // Only admin can update (approve/reject/create patient)
        return $user->role === 'admin';
    }

    /**
     * Determine if the user can delete the appointment request.
     */
    public function delete(User $user, AppointmentRequest $appointmentRequest): bool
    {
        return $user->role === 'admin';
    }
}
