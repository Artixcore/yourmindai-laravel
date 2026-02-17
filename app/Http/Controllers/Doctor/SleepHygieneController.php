<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\PatientProfile;
use App\Models\SleepHygieneItem;
use App\Models\SleepHygieneLog;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SleepHygieneController extends Controller
{
    private function getPatientProfile(Patient $patient): ?PatientProfile
    {
        $profile = $patient->resolvePatientProfile();
        if ($profile) {
            return $profile;
        }
        return PatientProfile::where('doctor_id', $patient->doctor_id)
            ->where(function ($query) use ($patient) {
                $query->where('full_name', $patient->name)
                    ->orWhere('phone', $patient->phone);
            })
            ->first();
    }

    private function canAccessPatient($user, $patient, ?PatientProfile $profile): bool
    {
        if ($user->role === 'admin') {
            return true;
        }
        if ($profile && $profile->doctor_id === $user->id) {
            return true;
        }
        if ($patient->doctor_id === $user->id) {
            return true;
        }
        return false;
    }

    public function index(Request $request, Patient $patient)
    {
        $patientProfile = $this->getPatientProfile($patient);

        if (!$patientProfile) {
            return back()->with('error', 'Patient profile not found.');
        }

        if (!$this->canAccessPatient($request->user(), $patient, $patientProfile)) {
            abort(403);
        }

        $startDate = $request->input('start_date', Carbon::now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));

        $logs = SleepHygieneLog::where(function ($q) use ($patientProfile, $patient) {
            $q->where('patient_profile_id', $patientProfile->id)
                ->orWhere('patient_id', $patient->id);
        })
            ->whereBetween('log_date', [$startDate, $endDate])
            ->with('item')
            ->orderBy('log_date', 'desc')
            ->orderBy('sleep_hygiene_item_id')
            ->paginate(50);

        return view('doctor.patients.sleep-hygiene.index', compact('patient', 'patientProfile', 'logs', 'startDate', 'endDate'));
    }
}
