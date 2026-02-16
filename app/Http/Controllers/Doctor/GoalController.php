<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Goal;
use App\Models\Patient;
use App\Models\PatientProfile;
use Illuminate\Http\Request;

class GoalController extends Controller
{
    public function index(Request $request, Patient $patient)
    {
        $patientProfile = $this->resolvePatientProfile($patient);

        if (!$this->canAccessPatient($request->user(), $patientProfile)) {
            abort(403);
        }

        $goals = Goal::where('patient_id', $patientProfile->id)
            ->orderBy('start_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('doctor.patients.goals.index', compact('patient', 'patientProfile', 'goals'));
    }

    private function resolvePatientProfile(Patient $patient): PatientProfile
    {
        $profile = $patient->resolvePatientProfile();
        if (!$profile) {
            abort(404, 'Patient profile not found for this patient.');
        }
        return $profile;
    }

    public function create(Request $request, Patient $patient)
    {
        $patientProfile = $this->resolvePatientProfile($patient);

        if (!$this->canAccessPatient($request->user(), $patientProfile)) {
            abort(403);
        }

        return view('doctor.patients.goals.create', compact('patient', 'patientProfile'));
    }

    public function store(Request $request, Patient $patient)
    {
        $patientProfile = $this->resolvePatientProfile($patient);

        if (!$this->canAccessPatient($request->user(), $patientProfile)) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'nullable|string|max:50',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'frequency_per_day' => 'nullable|integer|min:1|max:100',
            'duration_minutes' => 'nullable|integer|min:1|max:480',
            'visible_to_patient' => 'boolean',
            'visible_to_parent' => 'boolean',
        ]);

        $validated['patient_id'] = $patientProfile->id;
        $validated['visible_to_patient'] = $request->boolean('visible_to_patient');
        $validated['visible_to_parent'] = $request->boolean('visible_to_parent');

        Goal::create($validated);

        return redirect()->route('patients.goals.index', $patient)
            ->with('success', 'Goal created successfully.');
    }

    public function edit(Request $request, Patient $patient, Goal $goal)
    {
        $patientProfile = $this->resolvePatientProfile($patient);

        if (!$this->canAccessPatient($request->user(), $patientProfile)) {
            abort(403);
        }

        if ($goal->patient_id != $patientProfile->id) {
            abort(404);
        }

        return view('doctor.patients.goals.edit', compact('patient', 'patientProfile', 'goal'));
    }

    public function update(Request $request, Patient $patient, Goal $goal)
    {
        $patientProfile = $this->resolvePatientProfile($patient);

        if (!$this->canAccessPatient($request->user(), $patientProfile)) {
            abort(403);
        }

        if ($goal->patient_id != $patientProfile->id) {
            abort(404);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'nullable|string|max:50',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'frequency_per_day' => 'nullable|integer|min:1|max:100',
            'duration_minutes' => 'nullable|integer|min:1|max:480',
            'visible_to_patient' => 'boolean',
            'visible_to_parent' => 'boolean',
        ]);

        $validated['visible_to_patient'] = $request->boolean('visible_to_patient');
        $validated['visible_to_parent'] = $request->boolean('visible_to_parent');

        $goal->update($validated);

        return redirect()->route('patients.goals.index', $patient)
            ->with('success', 'Goal updated successfully.');
    }

    public function destroy(Request $request, Patient $patient, Goal $goal)
    {
        $patientProfile = $this->resolvePatientProfile($patient);

        if (!$this->canAccessPatient($request->user(), $patientProfile)) {
            abort(403);
        }

        if ($goal->patient_id != $patientProfile->id) {
            abort(404);
        }

        $goal->delete();

        return redirect()->route('patients.goals.index', $patient)
            ->with('success', 'Goal deleted successfully.');
    }

    private function canAccessPatient($user, $patient): bool
    {
        return $user->isAdmin() || $user->id === $patient->doctor_id;
    }
}
