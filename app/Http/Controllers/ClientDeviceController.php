<?php

namespace App\Http\Controllers;

use App\Models\PatientProfile;
use App\Models\Patient;
use App\Models\PatientDevice;
use Illuminate\Http\Request;

class ClientDeviceController extends Controller
{
    /**
     * Get patient ID helper
     */
    private function getPatientId()
    {
        $user = auth()->user();
        $patientProfile = PatientProfile::where('user_id', $user->id)->first();
        $patient = Patient::where('email', $user->email)->first();
        
        if ($patientProfile) {
            return ['id' => $patientProfile->id, 'is_profile' => true];
        } elseif ($patient) {
            return ['id' => $patient->id, 'is_profile' => false];
        }
        
        return null;
    }

    /**
     * Display a listing of registered devices.
     */
    public function index()
    {
        $patientInfo = $this->getPatientId();
        
        if (!$patientInfo) {
            return redirect()->route('client.dashboard')
                ->with('error', 'Patient profile not found.');
        }

        $devices = PatientDevice::where(
            $patientInfo['is_profile'] ? 'patient_profile_id' : 'patient_id',
            $patientInfo['id']
        )
        ->orderBy('last_active_at', 'desc')
        ->get();

        return view('client.devices.index', compact('devices'));
    }

    /**
     * Store a newly registered device.
     */
    public function store(Request $request)
    {
        $patientInfo = $this->getPatientId();
        
        if (!$patientInfo) {
            return back()->with('error', 'Patient profile not found.');
        }

        $request->validate([
            'device_name' => 'required|string|max:255',
            'device_type' => 'required|in:mobile,tablet,desktop,wearable,smartwatch,other',
            'device_identifier' => 'nullable|string|max:100',
            'os_type' => 'nullable|string|max:50',
            'os_version' => 'nullable|string|max:50',
            'app_version' => 'nullable|string|max:50',
            'notes' => 'nullable|string|max:500',
            'device_source' => 'nullable|in:app_registered,manual',
        ]);

        $identifier = $request->device_identifier;
        if (empty($identifier)) {
            $identifier = \Illuminate\Support\Str::uuid()->toString();
        }
        // Ensure unique: append random suffix if collision
        while (\App\Models\PatientDevice::where('device_identifier', $identifier)->exists()) {
            $identifier = \Illuminate\Support\Str::uuid()->toString();
        }

        $deviceData = [
            'device_name' => $request->device_name,
            'device_type' => $request->device_type,
            'device_identifier' => $identifier,
            'device_source' => $request->device_source ?? 'app_registered',
            'os_type' => $request->os_type,
            'os_version' => $request->os_version,
            'app_version' => $request->app_version,
            'notes' => $request->notes,
            'last_active_at' => now(),
            'is_active' => true,
            'registered_at' => now(),
        ];

        if ($patientInfo['is_profile']) {
            $deviceData['patient_profile_id'] = $patientInfo['id'];
        } else {
            $deviceData['patient_id'] = $patientInfo['id'];
        }

        PatientDevice::create($deviceData);

        return redirect()->route('client.devices.index')
            ->with('success', 'Device registered successfully.');
    }

    /**
     * Remove a device.
     */
    public function destroy(PatientDevice $device)
    {
        $patientInfo = $this->getPatientId();
        
        if (!$patientInfo) {
            return back()->with('error', 'Patient profile not found.');
        }

        // Verify device belongs to patient
        $devicePatientId = $patientInfo['is_profile'] 
            ? $device->patient_profile_id 
            : $device->patient_id;

        if ($devicePatientId != $patientInfo['id']) {
            return back()->with('error', 'Unauthorized action.');
        }

        $device->delete();

        return redirect()->route('client.devices.index')
            ->with('success', 'Device removed successfully.');
    }
}
