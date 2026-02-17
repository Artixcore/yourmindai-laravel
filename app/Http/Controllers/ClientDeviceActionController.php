<?php

namespace App\Http\Controllers;

use App\Models\PatientProfile;
use App\Models\Patient;
use App\Models\PatientDevice;
use App\Models\DeviceAction;
use App\Http\Requests\StoreDeviceActionRequest;
use Illuminate\Http\Request;

class ClientDeviceActionController extends Controller
{
    private function getPatientId()
    {
        $user = auth()->user();
        $patientProfile = PatientProfile::where('user_id', $user->id)->first();
        $patient = Patient::where('email', $user->email)->first();

        if ($patientProfile) {
            return ['id' => $patientProfile->id, 'is_profile' => true];
        }
        if ($patient) {
            return ['id' => $patient->id, 'is_profile' => false];
        }

        return null;
    }

    public function index(Request $request)
    {
        $patientInfo = $this->getPatientId();

        if (!$patientInfo) {
            return redirect()->route('client.dashboard')
                ->with('error', 'Patient profile not found.');
        }

        $actions = DeviceAction::where(
            $patientInfo['is_profile'] ? 'patient_profile_id' : 'patient_id',
            $patientInfo['id']
        )
            ->with('device')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $devices = PatientDevice::where(
            $patientInfo['is_profile'] ? 'patient_profile_id' : 'patient_id',
            $patientInfo['id']
        )
            ->orderBy('device_name')
            ->get();

        return view('client.device-actions.index', compact('actions', 'devices'));
    }

    public function store(StoreDeviceActionRequest $request)
    {
        $patientInfo = $this->getPatientId();

        if (!$patientInfo) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Patient profile not found.'], 403);
            }
            return redirect()->route('client.dashboard')->with('error', 'Patient profile not found.');
        }

        $deviceId = $request->device_id;
        if ($deviceId) {
            $device = PatientDevice::find($deviceId);
            if (!$device) {
                return response()->json(['success' => false, 'message' => 'Invalid device.', 'errors' => ['device_id' => ['Invalid device.']]], 422);
            }
            $deviceBelongsTo = $patientInfo['is_profile']
                ? $device->patient_profile_id == $patientInfo['id']
                : $device->patient_id == $patientInfo['id'];
            if (!$deviceBelongsTo) {
                return response()->json(['success' => false, 'message' => 'Invalid device.'], 403);
            }
        }

        $data = [
            'action_type' => $request->action_type,
            'device_id' => $deviceId,
            'action_note' => $request->action_note,
        ];
        if ($patientInfo['is_profile']) {
            $data['patient_profile_id'] = $patientInfo['id'];
        } else {
            $data['patient_id'] = $patientInfo['id'];
        }

        $action = DeviceAction::create($data);

        if ($request->ajax() || $request->wantsJson()) {
            $html = view('client.device-actions._action_row', ['action' => $action->load('device')])->render();
            return response()->json([
                'success' => true,
                'message' => 'Action logged successfully.',
                'html' => $html,
                'target' => '#device-actions-list',
                'prepend' => true,
                'hideOnAdd' => '#device-actions-empty',
                'showTarget' => true,
            ]);
        }

        return redirect()->route('client.device-actions.index')
            ->with('success', 'Action logged successfully.');
    }
}
