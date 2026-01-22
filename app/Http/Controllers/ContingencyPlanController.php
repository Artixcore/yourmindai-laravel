<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\PatientProfile;
use App\Models\ContingencyPlan;
use App\Models\ContingencyActivation;
use Illuminate\Http\Request;

class ContingencyPlanController extends Controller
{
    /**
     * Get PatientProfile from Patient model
     */
    private function getPatientProfile(Patient $patient)
    {
        // Try to find PatientProfile by user_id if patient has a user
        if ($patient->email) {
            $user = \App\Models\User::where('email', $patient->email)->first();
            if ($user) {
                $patientProfile = PatientProfile::where('user_id', $user->id)->first();
                if ($patientProfile) {
                    return $patientProfile;
                }
            }
        }
        
        // Try to find by matching doctor_id and name/email
        $patientProfile = PatientProfile::where('doctor_id', $patient->doctor_id)
            ->where(function($query) use ($patient) {
                $query->where('full_name', $patient->name)
                      ->orWhere('phone', $patient->phone);
            })
            ->first();
        
        return $patientProfile;
    }

    /**
     * Display a listing of contingency plans for a patient.
     */
    public function index(Patient $patient)
    {
        $patientProfile = $this->getPatientProfile($patient);
        
        if (!$patientProfile) {
            return back()->with('error', 'Patient profile not found. Please ensure the patient has a profile.');
        }
        
        // Ensure doctor can only see plans for their patients
        $doctorId = auth()->id();
        $plans = ContingencyPlan::where(function($query) use ($patientProfile, $patient) {
                $query->where('patient_profile_id', $patientProfile->id)
                      ->orWhere('patient_id', $patient->id);
            })
            ->where('created_by_doctor_id', $doctorId)
            ->with('createdByDoctor', 'activations')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('doctor.patients.contingency.index', compact('patient', 'patientProfile', 'plans'));
    }

    /**
     * Show the form for creating a new plan.
     */
    public function create(Patient $patient)
    {
        return view('doctor.patients.contingency.create', compact('patient'));
    }

    /**
     * Store a newly created plan.
     */
    public function store(Request $request, Patient $patient)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'trigger_conditions' => 'required|array|min:1',
            'actions' => 'required|array|min:1',
            'emergency_contacts' => 'nullable|array',
            'status' => 'required|in:active,inactive',
        ]);

        $patientProfile = $this->getPatientProfile($patient);
        
        if (!$patientProfile) {
            return back()->with('error', 'Patient profile not found. Please ensure the patient has a profile.');
        }

        // Process trigger conditions - extract descriptions if they're objects
        $triggerConditions = collect($request->trigger_conditions)->map(function($condition) {
            return is_array($condition) && isset($condition['description']) 
                ? $condition['description'] 
                : (is_string($condition) ? $condition : '');
        })->filter()->values()->toArray();
        
        // Process actions - extract descriptions if they're objects
        $actions = collect($request->actions)->map(function($action) {
            return is_array($action) && isset($action['description']) 
                ? $action['description'] 
                : (is_string($action) ? $action : '');
        })->filter()->values()->toArray();

        ContingencyPlan::create([
            'patient_profile_id' => $patientProfile->id,
            'patient_id' => $patient->id,
            'created_by_doctor_id' => auth()->id(),
            'title' => $request->title,
            'trigger_conditions' => $triggerConditions,
            'actions' => $actions,
            'emergency_contacts' => $request->emergency_contacts ?? [],
            'status' => $request->status,
        ]);

        return redirect()->route('patients.contingency-plans.index', $patient)
            ->with('success', 'Contingency plan created successfully.');
    }

    /**
     * Display the specified plan.
     */
    public function show(Patient $patient, ContingencyPlan $contingencyPlan)
    {
        $patientProfile = $this->getPatientProfile($patient);
        $contingencyPlan->load('createdByDoctor', 'activations');
        return view('doctor.patients.contingency.show', compact('patient', 'patientProfile', 'contingencyPlan'));
    }

    /**
     * Show the form for editing the specified plan.
     */
    public function edit(Patient $patient, ContingencyPlan $contingencyPlan)
    {
        return view('doctor.patients.contingency.edit', compact('patient', 'contingencyPlan'));
    }

    /**
     * Update the specified plan.
     */
    public function update(Request $request, Patient $patient, ContingencyPlan $contingencyPlan)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'trigger_conditions' => 'required|array|min:1',
            'actions' => 'required|array|min:1',
            'emergency_contacts' => 'nullable|array',
            'status' => 'required|in:active,inactive',
        ]);

        // Process trigger conditions - extract descriptions if they're objects
        $triggerConditions = collect($request->trigger_conditions)->map(function($condition) {
            return is_array($condition) && isset($condition['description']) 
                ? $condition['description'] 
                : (is_string($condition) ? $condition : '');
        })->filter()->values()->toArray();
        
        // Process actions - extract descriptions if they're objects
        $actions = collect($request->actions)->map(function($action) {
            return is_array($action) && isset($action['description']) 
                ? $action['description'] 
                : (is_string($action) ? $action : '');
        })->filter()->values()->toArray();

        $contingencyPlan->update([
            'title' => $request->title,
            'trigger_conditions' => $triggerConditions,
            'actions' => $actions,
            'emergency_contacts' => $request->emergency_contacts ?? [],
            'status' => $request->status,
        ]);

        return redirect()->route('patients.contingency-plans.index', $patient)
            ->with('success', 'Contingency plan updated successfully.');
    }

    /**
     * Remove the specified plan.
     */
    public function destroy(Patient $patient, ContingencyPlan $contingencyPlan)
    {
        $contingencyPlan->delete();

        return redirect()->route('patients.contingency-plans.index', $patient)
            ->with('success', 'Contingency plan deleted successfully.');
    }

    /**
     * Manually activate a contingency plan (doctor/admin).
     */
    public function activate(Request $request, Patient $patient, ContingencyPlan $contingencyPlan)
    {
        $request->validate([
            'trigger_reason' => 'nullable|string|max:1000',
        ]);

        $reason = $request->trigger_reason ?? 'Manually activated by healthcare provider';
        $activation = $contingencyPlan->activate('doctor', $reason);

        return back()->with('success', 'Contingency plan activated successfully.');
    }
}
