<?php

namespace App\Policies;

use App\Models\SessionReport;
use App\Models\User;

class SessionReportPolicy
{
    /**
     * Determine if the user can view any session reports.
     */
    public function viewAny(User $user): bool
    {
        // Admin can view all reports
        if ($user->role === 'admin') {
            return true;
        }

        // Doctors can view their own reports
        return $user->role === 'doctor';
    }

    /**
     * Determine if the user can view the session report.
     */
    public function view(User $user, SessionReport $report): bool
    {
        // Admin can view all
        if ($user->role === 'admin') {
            return true;
        }

        // Doctor can view their own reports
        if ($user->role === 'doctor') {
            return $report->created_by === $user->id;
        }

        return false;
    }

    /**
     * Determine if the user can create session reports.
     */
    public function create(User $user): bool
    {
        // Both admin and doctor can create reports
        return in_array($user->role, ['admin', 'doctor']);
    }

    /**
     * Determine if the user can update the session report.
     */
    public function update(User $user, SessionReport $report): bool
    {
        // Cannot update finalized reports
        if ($report->finalized_at) {
            return false;
        }

        // Admin can update any non-finalized report
        if ($user->role === 'admin') {
            return true;
        }

        // Doctor can only update their own non-finalized reports
        if ($user->role === 'doctor') {
            return $report->created_by === $user->id;
        }

        return false;
    }

    /**
     * Determine if the user can delete the session report.
     */
    public function delete(User $user, SessionReport $report): bool
    {
        // Cannot delete finalized reports
        if ($report->finalized_at) {
            return false;
        }

        // Admin can delete non-finalized reports
        if ($user->role === 'admin') {
            return true;
        }

        // Doctor can only delete their own non-finalized reports
        if ($user->role === 'doctor') {
            return $report->created_by === $user->id;
        }

        return false;
    }

    /**
     * Determine if the user can finalize the session report.
     */
    public function finalize(User $user, SessionReport $report): bool
    {
        // Only the creator can finalize
        return $report->created_by === $user->id && !$report->finalized_at;
    }
}
