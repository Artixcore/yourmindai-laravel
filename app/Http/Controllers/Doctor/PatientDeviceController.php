<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PatientDevice;
use App\Models\PatientProfile;
use App\Models\User;

class PatientDeviceController extends Controller
{
    public function index(Request $request, PatientProfile $patient)
    {
        $user = $request->user();
        
        if (!$this->canAccessPatient($user, $patient)) {
            abort(403, 'Unauthorized access to patient data');
        }

        $devices = PatientDevice::where('patient_profile_id', $patient->id)
            ->orderBy('last_active_at', 'desc')
            ->get();

        // Calculate statistics for this patient
        $stats = [
            'total' => $devices->count(),
            'active' => $devices->where('last_active_at', '>=', now()->subDays(7))->count(),
            'by_type' => $devices->groupBy('device_type')->map->count(),
        ];

        return view('doctor.patients.devices.index', compact('patient', 'devices', 'stats'));
    }

    public function show(Request $request, PatientProfile $patient, PatientDevice $device)
    {
        $user = $request->user();
        
        if (!$this->canAccessPatient($user, $patient)) {
            abort(403, 'Unauthorized access to patient data');
        }

        // Verify device belongs to this patient
        if ($device->patient_profile_id != $patient->id) {
            abort(404);
        }

        // Get device activity information
        $activityInfo = [
            'first_seen' => $device->created_at,
            'last_active' => $device->last_active_at,
            'days_since_registration' => $device->created_at->diffInDays(now()),
            'days_since_last_active' => $device->last_active_at ? $device->last_active_at->diffInDays(now()) : null,
        ];

        return view('doctor.patients.devices.show', compact('patient', 'device', 'activityInfo'));
    }

    protected function canAccessPatient(User $user, PatientProfile $patient): bool
    {
        if ($user->role === 'admin') {
            return true;
        }
        
        if ($user->role === 'doctor') {
            return $patient->doctor_id === $user->id || 
                   $user->assignedDoctors()->where('doctor_id', $patient->doctor_id)->exists();
        }
        
        return false;
    }
}
