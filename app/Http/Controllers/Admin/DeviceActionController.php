<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DeviceAction;
use App\Models\PatientProfile;
use App\Models\Patient;
use Illuminate\Http\Request;

class DeviceActionController extends Controller
{
    public function index(Request $request)
    {
        $patientId = $request->input('patient_id');

        $query = DeviceAction::with(['device', 'patientProfile.user', 'patient'])
            ->orderBy('created_at', 'desc');

        if ($patientId) {
            $query->where(function ($q) use ($patientId) {
                $q->where('patient_profile_id', $patientId)
                    ->orWhere('patient_id', $patientId);
            });
        }

        $actions = $query->paginate(50)->withQueryString();

        $patientProfiles = PatientProfile::with('user')->orderBy('full_name')->get();
        $patients = Patient::orderBy('name')->get(['id', 'name', 'email']);

        return view('admin.device-actions.index', compact('actions', 'patientProfiles', 'patients', 'patientId'));
    }
}
