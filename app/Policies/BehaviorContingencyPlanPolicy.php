<?php

namespace App\Policies;

use App\Models\User;
use App\Models\BehaviorContingencyPlan;
use App\Models\PatientProfile;
use App\Models\Patient;

class BehaviorContingencyPlanPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, BehaviorContingencyPlan $plan): bool
    {
        if (strtolower($user->role ?? '') === 'patient' || $user->role === 'PATIENT') {
            return $this->isOwnPlan($user, $plan);
        }

        if (in_array($user->role, ['admin', 'doctor', 'assistant'])) {
            return $this->isPatientOfDoctor($user, $plan);
        }

        return false;
    }

    public function create(User $user): bool
    {
        return in_array($user->role, ['admin', 'doctor']);
    }

    public function update(User $user, BehaviorContingencyPlan $plan): bool
    {
        return in_array($user->role, ['admin', 'doctor']) && $this->isPatientOfDoctor($user, $plan);
    }

    public function delete(User $user, BehaviorContingencyPlan $plan): bool
    {
        return in_array($user->role, ['admin', 'doctor']) && $this->isPatientOfDoctor($user, $plan);
    }

    public function checkin(User $user, BehaviorContingencyPlan $plan): bool
    {
        return $this->isOwnPlan($user, $plan) && $plan->isActive();
    }

    private function isOwnPlan(User $user, BehaviorContingencyPlan $plan): bool
    {
        if ($plan->patient_profile_id) {
            $patientProfile = PatientProfile::where('user_id', $user->id)
                ->where('id', $plan->patient_profile_id)
                ->first();

            if ($patientProfile) {
                return true;
            }
        }

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

    private function isPatientOfDoctor(User $user, BehaviorContingencyPlan $plan): bool
    {
        if ($plan->patient_profile_id) {
            $patientProfile = PatientProfile::where('doctor_id', $user->id)
                ->where('id', $plan->patient_profile_id)
                ->first();

            if ($patientProfile) {
                return true;
            }
        }

        if ($plan->patient_id) {
            $patient = Patient::where('doctor_id', $user->id)
                ->where('id', $plan->patient_id)
                ->first();

            if ($patient) {
                return true;
            }
        }

        return $user->role === 'admin';
    }
}
