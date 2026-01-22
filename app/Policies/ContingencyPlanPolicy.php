<?php

namespace App\Policies;

use App\Models\User;
use App\Models\ContingencyPlan;
use App\Models\PatientProfile;
use App\Models\Patient;

class ContingencyPlanPolicy
{
    /**
     * Determine if the user can view any plans.
     */
    public function viewAny(User $user): bool
    {
        return true; // Both patients and doctors can view plans
    }

    /**
     * Determine if the user can view the plan.
     */
    public function view(User $user, ContingencyPlan $plan): bool
    {
        // Patients can view their own plans
        if ($user->role === 'PATIENT') {
            return $this->isOwnPlan($user, $plan);
        }
        
        // Doctors/admins can view plans of their patients
        if (in_array($user->role, ['admin', 'doctor', 'assistant'])) {
            return $this->isPatientOfDoctor($user, $plan);
        }
        
        return false;
    }

    /**
     * Determine if the user can create plans.
     */
    public function create(User $user): bool
    {
        // Only doctors/admins can create plans
        return in_array($user->role, ['admin', 'doctor']);
    }

    /**
     * Determine if the user can update the plan.
     */
    public function update(User $user, ContingencyPlan $plan): bool
    {
        // Only doctors/admins can update plans
        return in_array($user->role, ['admin', 'doctor']);
    }

    /**
     * Determine if the user can delete the plan.
     */
    public function delete(User $user, ContingencyPlan $plan): bool
    {
        // Only doctors/admins can delete plans
        return in_array($user->role, ['admin', 'doctor']);
    }

    /**
     * Determine if the user can activate the plan.
     */
    public function activate(User $user, ContingencyPlan $plan): bool
    {
        // Patients can activate their own active plans
        if ($user->role === 'PATIENT') {
            return $this->isOwnPlan($user, $plan) && $plan->isActive();
        }
        
        // Doctors/admins can activate plans
        return in_array($user->role, ['admin', 'doctor']);
    }

    /**
     * Check if plan belongs to the user's patient profile
     */
    private function isOwnPlan(User $user, ContingencyPlan $plan): bool
    {
        // Check patient_profile_id
        if ($plan->patient_profile_id) {
            $patientProfile = PatientProfile::where('user_id', $user->id)
                ->where('id', $plan->patient_profile_id)
                ->first();
            
            if ($patientProfile) {
                return true;
            }
        }
        
        // Check patient_id
        if ($plan->patient_id) {
            $patient = Patient::where('email', $user->email)
                ->where('id', $plan->patient_id)
                ->first();
            
            if ($patient) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Check if plan belongs to a patient of the doctor
     */
    private function isPatientOfDoctor(User $user, ContingencyPlan $plan): bool
    {
        // Check patient_profile_id
        if ($plan->patient_profile_id) {
            $patientProfile = PatientProfile::where('doctor_id', $user->id)
                ->where('id', $plan->patient_profile_id)
                ->first();
            
            if ($patientProfile) {
                return true;
            }
        }
        
        // Check patient_id
        if ($plan->patient_id) {
            $patient = Patient::where('doctor_id', $user->id)
                ->where('id', $plan->patient_id)
                ->first();
            
            if ($patient) {
                return true;
            }
        }
        
        // Admins can view all
        return $user->role === 'admin';
    }
}
