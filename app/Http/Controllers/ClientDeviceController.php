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
            'device_type' => 'required|in:mobile,tablet,desktop',
            'device_identifier' => 'required|string|max:100|unique:patient_devices,device_identifier',
            'os_type' => 'nullable|string|max:50',
            'os_version' => 'nullable|string|max:50',
            'app_version' => 'nullable|string|max:50',
        ]);

        $deviceData = [
            'device_name' => $request->device_name,
            'device_type' => $request->device_type,
            'device_identifier' => $request->device_identifier,
            'os_type' => $request->os_type,
            'os_version' => $request->os_version,
            'app_version' => $request->app_version,
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
