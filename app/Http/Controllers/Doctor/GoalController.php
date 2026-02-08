<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Goal;
use App\Models\PatientProfile;
use Illuminate\Http\Request;

class GoalController extends Controller
{
    public function index(Request $request, $patientId)
    {
        $patient = PatientProfile::with('user')->findOrFail($patientId);

        if (!$this->canAccessPatient($request->user(), $patient)) {
            abort(403);
        }

        $goals = Goal::where('patient_id', $patient->id)
            ->orderBy('start_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('doctor.patients.goals.index', compact('patient', 'goals'));
    }

    public function create(Request $request, $patientId)
    {
        $patient = PatientProfile::with('user')->findOrFail($patientId);

        if (!$this->canAccessPatient($request->user(), $patient)) {
            abort(403);
        }

        return view('doctor.patients.goals.create', compact('patient'));
    }

    public function store(Request $request, $patientId)
    {
        $patient = PatientProfile::findOrFail($patientId);

        if (!$this->canAccessPatient($request->user(), $patient)) {
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

        $validated['patient_id'] = $patient->id;
        $validated['visible_to_patient'] = $request->boolean('visible_to_patient');
        $validated['visible_to_parent'] = $request->boolean('visible_to_parent');

        Goal::create($validated);

        return redirect()->route('patients.goals.index', $patient->id)
            ->with('success', 'Goal created successfully.');
    }

    public function edit(Request $request, $patientId, Goal $goal)
    {
        $patient = PatientProfile::with('user')->findOrFail($patientId);

        if (!$this->canAccessPatient($request->user(), $patient)) {
            abort(403);
        }

        if ($goal->patient_id != $patient->id) {
            abort(404);
        }

        return view('doctor.patients.goals.edit', compact('patient', 'goal'));
    }

    public function update(Request $request, $patientId, Goal $goal)
    {
        $patient = PatientProfile::findOrFail($patientId);

        if (!$this->canAccessPatient($request->user(), $patient)) {
            abort(403);
        }

        if ($goal->patient_id != $patient->id) {
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

        return redirect()->route('patients.goals.index', $patient->id)
            ->with('success', 'Goal updated successfully.');
    }

    public function destroy(Request $request, $patientId, Goal $goal)
    {
        $patient = PatientProfile::findOrFail($patientId);

        if (!$this->canAccessPatient($request->user(), $patient)) {
            abort(403);
        }

        if ($goal->patient_id != $patient->id) {
            abort(404);
        }

        $goal->delete();

        return redirect()->route('patients.goals.index', $patient->id)
            ->with('success', 'Goal deleted successfully.');
    }

    private function canAccessPatient($user, $patient): bool
    {
        return $user->isAdmin() || $user->id === $patient->doctor_id;
    }
}
