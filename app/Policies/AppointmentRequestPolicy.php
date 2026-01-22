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
        return $user->role === 'admin';
    }

    /**
     * Determine if the user can view the appointment request.
     */
    public function view(User $user, AppointmentRequest $appointmentRequest): bool
    {
        return $user->role === 'admin';
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
