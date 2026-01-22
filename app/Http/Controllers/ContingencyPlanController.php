<?php

namespace App\Http\Controllers;

use App\Models\PatientProfile;
use App\Models\ContingencyPlan;
use App\Models\ContingencyActivation;
use Illuminate\Http\Request;

class ContingencyPlanController extends Controller
{
    /**
     * Display a listing of contingency plans for a patient.
     */
    public function index(PatientProfile $patient)
    {
        $plans = ContingencyPlan::where('patient_profile_id', $patient->id)
            ->with('createdByDoctor', 'activations')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('doctor.patients.contingency.index', compact('patient', 'plans'));
    }

    /**
     * Show the form for creating a new plan.
     */
    public function create(PatientProfile $patient)
    {
        return view('doctor.patients.contingency.create', compact('patient'));
    }

    /**
     * Store a newly created plan.
     */
    public function store(Request $request, PatientProfile $patient)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'trigger_conditions' => 'required|array|min:1',
            'actions' => 'required|array|min:1',
            'emergency_contacts' => 'nullable|array',
            'status' => 'required|in:active,inactive',
        ]);

        ContingencyPlan::create([
            'patient_profile_id' => $patient->id,
            'created_by_doctor_id' => auth()->id(),
            'title' => $request->title,
            'trigger_conditions' => $request->trigger_conditions,
            'actions' => $request->actions,
            'emergency_contacts' => $request->emergency_contacts ?? [],
            'status' => $request->status,
        ]);

        return redirect()->route('patients.contingency-plans.index', $patient)
            ->with('success', 'Contingency plan created successfully.');
    }

    /**
     * Display the specified plan.
     */
    public function show(PatientProfile $patient, ContingencyPlan $contingencyPlan)
    {
        $contingencyPlan->load('createdByDoctor', 'activations');
        return view('doctor.patients.contingency.show', compact('patient', 'contingencyPlan'));
    }

    /**
     * Show the form for editing the specified plan.
     */
    public function edit(PatientProfile $patient, ContingencyPlan $contingencyPlan)
    {
        return view('doctor.patients.contingency.edit', compact('patient', 'contingencyPlan'));
    }

    /**
     * Update the specified plan.
     */
    public function update(Request $request, PatientProfile $patient, ContingencyPlan $contingencyPlan)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'trigger_conditions' => 'required|array|min:1',
            'actions' => 'required|array|min:1',
            'emergency_contacts' => 'nullable|array',
            'status' => 'required|in:active,inactive',
        ]);

        $contingencyPlan->update([
            'title' => $request->title,
            'trigger_conditions' => $request->trigger_conditions,
            'actions' => $request->actions,
            'emergency_contacts' => $request->emergency_contacts ?? [],
            'status' => $request->status,
        ]);

        return redirect()->route('patients.contingency-plans.index', $patient)
            ->with('success', 'Contingency plan updated successfully.');
    }

    /**
     * Remove the specified plan.
     */
    public function destroy(PatientProfile $patient, ContingencyPlan $contingencyPlan)
    {
        $contingencyPlan->delete();

        return redirect()->route('patients.contingency-plans.index', $patient)
            ->with('success', 'Contingency plan deleted successfully.');
    }

    /**
     * Manually activate a contingency plan (doctor/admin).
     */
    public function activate(Request $request, PatientProfile $patient, ContingencyPlan $contingencyPlan)
    {
        $request->validate([
            'trigger_reason' => 'required|string|max:1000',
        ]);

        $activation = $contingencyPlan->activate('doctor', $request->trigger_reason);

        return back()->with('success', 'Contingency plan activated successfully.');
    }
}
