<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\PatientProfile;
use App\Models\SleepHygieneLog;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SleepHygieneController extends Controller
{
    public function index(Request $request)
    {
        $patientId = $request->input('patient_id');
        $startDate = $request->input('start_date', Carbon::now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));

        $query = SleepHygieneLog::with(['item', 'patientProfile.user', 'patient'])
            ->whereBetween('log_date', [$startDate, $endDate]);

        if ($patientId) {
            $query->where(function ($q) use ($patientId) {
                $q->where('patient_profile_id', $patientId)
                    ->orWhere('patient_id', $patientId);
            });
        }

        $logs = $query->orderBy('log_date', 'desc')
            ->orderBy('sleep_hygiene_item_id')
            ->paginate(50)
            ->withQueryString();

        $patientProfiles = PatientProfile::with('user')->orderBy('full_name')->get();
        $patients = Patient::orderBy('name')->get(['id', 'name', 'email']);

        return view('admin.sleep-hygiene.index', compact('logs', 'patientProfiles', 'patients', 'startDate', 'endDate', 'patientId'));
    }
}
