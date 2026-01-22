<?php

namespace App\Policies;

use App\Models\User;
use App\Models\PatientDevice;
use App\Models\PatientProfile;
use App\Models\Patient;

class PatientDevicePolicy
{
    /**
     * Determine if the user can view any devices.
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['admin', 'doctor', 'assistant']);
    }

    /**
     * Determine if the user can view the device.
     */
    public function view(User $user, PatientDevice $device): bool
    {
        // Patients can view their own devices
        if ($user->role === 'PATIENT') {
            return $this->isOwnDevice($user, $device);
        }
        
        // Doctors/admins can view devices of their patients
        return in_array($user->role, ['admin', 'doctor', 'assistant']);
    }

    /**
     * Determine if the user can create devices.
     */
    public function create(User $user): bool
    {
        // Patients can register their own devices
        return $user->role === 'PATIENT';
    }

    /**
     * Determine if the user can update the device.
     */
    public function update(User $user, PatientDevice $device): bool
    {
        // Patients can update their own devices
        if ($user->role === 'PATIENT') {
            return $this->isOwnDevice($user, $device);
        }
        
        // Doctors/admins can update devices
        return in_array($user->role, ['admin', 'doctor']);
    }

    /**
     * Determine if the user can delete the device.
     */
    public function delete(User $user, PatientDevice $device): bool
    {
        // Patients can delete their own devices
        if ($user->role === 'PATIENT') {
            return $this->isOwnDevice($user, $device);
        }
        
        // Doctors/admins can delete devices
        return in_array($user->role, ['admin', 'doctor']);
    }

    /**
     * Check if device belongs to the user's patient profile
     */
    private function isOwnDevice(User $user, PatientDevice $device): bool
    {
        // Check patient_profile_id
        if ($device->patient_profile_id) {
            $patientProfile = PatientProfile::where('user_id', $user->id)
                ->where('id', $device->patient_profile_id)
                ->first();
            
            if ($patientProfile) {
                return true;
            }
        }
        
        // Check patient_id
        if ($device->patient_id) {
            $patient = Patient::where('email', $user->email)
                ->where('id', $device->patient_id)
                ->first();
            
            if ($patient) {
                return true;
            }
        }
        
        return false;
    }
}
