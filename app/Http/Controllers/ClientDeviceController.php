<?php

namespace App\Http\Controllers;

use App\Models\PatientProfile;
use App\Models\Patient;
use App\Models\PatientDevice;
use App\Http\Requests\StoreDeviceRequest;
use App\Http\Requests\UpdateDeviceRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
    public function store(StoreDeviceRequest $request)
    {
        $patientInfo = $this->getPatientId();
        
        if (!$patientInfo) {
            return back()->with('error', 'Patient profile not found.');
        }

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

        try {
            $device = PatientDevice::create($deviceData);
        } catch (\Exception $e) {
            Log::error('Device registration failed', [
                'user_id' => $request->user()->id,
                'route' => 'client.devices.store',
                'error' => $e->getMessage(),
            ]);
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Failed to register device. Please try again.', 'errors' => ['error' => ['Failed to register device. Please try again.']]], 422);
            }
            return back()->withInput()->with('error', 'Failed to register device. Please try again.');
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Device registered successfully.',
                'html' => view('partials.device_card', compact('device'))->render(),
                'target' => '#devices-list',
                'prepend' => true,
                'showTarget' => true,
                'hideOnAdd' => '#devices-empty-state',
            ]);
        }

        return redirect()->route('client.devices.index')
            ->with('success', 'Device registered successfully.');
    }

    /**
     * Remove a device.
     */
    public function destroy(Request $request, PatientDevice $device)
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

        $deviceId = $device->id;
        $device->delete();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Device removed successfully.',
                'html' => '',
                'target' => '#device-card-' . $deviceId,
                'replace' => true,
            ]);
        }

        return redirect()->route('client.devices.index')
            ->with('success', 'Device removed successfully.');
    }

    /**
     * Update a device.
     */
    public function update(UpdateDeviceRequest $request, PatientDevice $device)
    {
        $patientInfo = $this->getPatientId();

        if (!$patientInfo) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Patient profile not found.'], 403);
            }
            return back()->with('error', 'Patient profile not found.');
        }

        $devicePatientId = $patientInfo['is_profile']
            ? $device->patient_profile_id
            : $device->patient_id;

        if ($devicePatientId != $patientInfo['id']) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Unauthorized action.'], 403);
            }
            return back()->with('error', 'Unauthorized action.');
        }

        $device->update([
            'device_name' => $request->device_name,
            'device_type' => $request->device_type,
            'device_identifier' => $request->device_identifier ?? $device->device_identifier,
            'notes' => $request->notes,
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Device updated successfully.',
                'html' => view('partials.device_card', ['device' => $device->fresh()])->render(),
                'target' => '#device-card-' . $device->id,
                'replace' => true,
            ]);
        }

        return redirect()->route('client.devices.index')
            ->with('success', 'Device updated successfully.');
    }
}
