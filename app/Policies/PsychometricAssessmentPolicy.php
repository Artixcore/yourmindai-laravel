<?php

namespace App\Policies;

use App\Models\User;
use App\Models\PsychometricAssessment;
use App\Models\PatientProfile;
use App\Models\Patient;

class PsychometricAssessmentPolicy
{
    /**
     * Determine if the user can view any assessments.
     */
    public function viewAny(User $user): bool
    {
        return true; // Both patients and doctors can view assessments
    }

    /**
     * Determine if the user can view the assessment.
     */
    public function view(User $user, PsychometricAssessment $assessment): bool
    {
        // Patients can view their own assessments
        if ($user->role === 'PATIENT') {
            return $this->isOwnAssessment($user, $assessment);
        }
        
        // Doctors/admins can view assessments of their patients
        if (in_array($user->role, ['admin', 'doctor', 'assistant'])) {
            return $this->isPatientOfDoctor($user, $assessment);
        }
        
        return false;
    }

    /**
     * Determine if the user can create assessments.
     */
    public function create(User $user): bool
    {
        // Only doctors/admins can create assessments
        return in_array($user->role, ['admin', 'doctor']);
    }

    /**
     * Determine if the user can update the assessment.
     */
    public function update(User $user, PsychometricAssessment $assessment): bool
    {
        // Only doctors/admins can update assessments
        return in_array($user->role, ['admin', 'doctor']);
    }

    /**
     * Determine if the user can delete the assessment.
     */
    public function delete(User $user, PsychometricAssessment $assessment): bool
    {
        // Only doctors/admins can delete assessments
        return in_array($user->role, ['admin', 'doctor']);
    }

    /**
     * Determine if the user can complete the assessment.
     */
    public function complete(User $user, PsychometricAssessment $assessment): bool
    {
        // Patients can complete their own pending assessments
        if ($user->role === 'PATIENT') {
            return $this->isOwnAssessment($user, $assessment) && $assessment->status === 'pending';
        }
        
        return false;
    }

    /**
     * Check if assessment belongs to the user's patient profile
     */
    private function isOwnAssessment(User $user, PsychometricAssessment $assessment): bool
    {
        // Check patient_profile_id
        if ($assessment->patient_profile_id) {
            $patientProfile = PatientProfile::where('user_id', $user->id)
                ->where('id', $assessment->patient_profile_id)
                ->first();
            
            if ($patientProfile) {
                return true;
            }
        }
        
        // Check patient_id
        if ($assessment->patient_id) {
            $patient = Patient::where('email', $user->email)
                ->where('id', $assessment->patient_id)
                ->first();
            
            if ($patient) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Check if assessment belongs to a patient of the doctor
     */
    private function isPatientOfDoctor(User $user, PsychometricAssessment $assessment): bool
    {
        // Check patient_profile_id
        if ($assessment->patient_profile_id) {
            $patientProfile = PatientProfile::where('doctor_id', $user->id)
                ->where('id', $assessment->patient_profile_id)
                ->first();
            
            if ($patientProfile) {
                return true;
            }
        }
        
        // Check patient_id
        if ($assessment->patient_id) {
            $patient = Patient::where('doctor_id', $user->id)
                ->where('id', $assessment->patient_id)
                ->first();
            
            if ($patient) {
                return true;
            }
        }
        
        // Admins can view all
        return $user->role === 'admin';
    }
}
