<?php

namespace App\Http\Controllers;

use App\Models\LifestyleLog;
use App\Models\PatientProfile;
use App\Models\Patient;
use App\Models\WellbeingLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ClientWellbeingController extends Controller
{
    private function getPatientProfile()
    {
        $user = auth()->user();
        return PatientProfile::where('user_id', $user->id)->first();
    }

    /** Client panel (authenticated). */
    public function index(Request $request)
    {
        $patient = $this->getPatientProfile();
        if (!$patient) {
            return redirect()->route('client.dashboard')
                ->with('error', 'Patient profile not found.');
        }

        $wellbeingLogs = WellbeingLog::where('patient_profile_id', $patient->id)
            ->orderBy('log_date', 'desc')
            ->take(30)
            ->get();

        $lifestyleErrors = LifestyleLog::where('patient_id', $patient->id)
            ->where('type', 'lifestyle_error')
            ->orderBy('logged_date', 'desc')
            ->take(20)
            ->get();

        $todayLog = WellbeingLog::where('patient_profile_id', $patient->id)
            ->whereDate('log_date', today())
            ->first();

        return view('client.wellbeing.index', compact('wellbeingLogs', 'lifestyleErrors', 'todayLog'));
    }

    /** Store screentime / wellbeing log. */
    public function store(Request $request)
    {
        $patient = $this->getPatientProfile();
        if (!$patient) {
            return back()->with('error', 'Patient profile not found.');
        }

        $validated = $request->validate([
            'log_date' => 'required|date',
            'screentime_minutes' => 'nullable|integer|min:0|max:1440',
            'details' => 'nullable|array',
            'lifestyle_errors' => 'nullable|array',
        ]);

        try {
            WellbeingLog::updateOrCreate(
                [
                    'patient_profile_id' => $patient->id,
                    'log_date' => $validated['log_date'],
                ],
                [
                    'screentime_minutes' => $validated['screentime_minutes'] ?? null,
                    'details' => $validated['details'] ?? null,
                    'lifestyle_errors' => $validated['lifestyle_errors'] ?? null,
                ]
            );
        } catch (\Exception $e) {
            Log::error('Wellbeing log store failed', [
                'user_id' => auth()->id(),
                'route' => 'client.wellbeing.store',
                'error' => $e->getMessage(),
            ]);
            return back()->withInput()->with('error', 'Failed to save wellbeing log. Please try again.');
        }

        return redirect()->route('client.wellbeing.index')
            ->with('success', 'Wellbeing log saved.');
    }

    /** Public website (no auth). */
    public function publicIndex(Request $request)
    {
        return view('wellbeing.public');
    }
}
