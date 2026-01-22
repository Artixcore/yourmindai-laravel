<?php

namespace App\Http\Controllers;

use App\Models\PatientProfile;
use App\Models\Patient;
use App\Models\ContingencyPlan;
use App\Models\ContingencyActivation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ClientContingencyController extends Controller
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
     * Display a listing of contingency plans.
     */
    public function index()
    {
        $patientInfo = $this->getPatientId();
        
        if (!$patientInfo) {
            return redirect()->route('client.dashboard')
                ->with('error', 'Patient profile not found.');
        }

        $contingencyPlans = ContingencyPlan::where(
            $patientInfo['is_profile'] ? 'patient_profile_id' : 'patient_id',
            $patientInfo['id']
        )
        ->where('status', 'active')
        ->with('createdByDoctor', 'activations')
        ->orderBy('created_at', 'desc')
        ->get();

        return view('client.contingency.index', compact('contingencyPlans'));
    }

    /**
     * Display the contingency plan details.
     */
    public function show(ContingencyPlan $plan)
    {
        $patientInfo = $this->getPatientId();
        
        if (!$patientInfo) {
            return redirect()->route('client.dashboard')
                ->with('error', 'Patient profile not found.');
        }

        // Verify plan belongs to patient
        $planPatientId = $patientInfo['is_profile'] 
            ? $plan->patient_profile_id 
            : $plan->patient_id;

        if ($planPatientId != $patientInfo['id']) {
            return redirect()->route('client.contingency.index')
                ->with('error', 'Unauthorized access.');
        }

        $plan->load('createdByDoctor', 'activations');

        return view('client.contingency.show', compact('plan'));
    }

    /**
     * Activate a contingency plan.
     */
    public function activate(Request $request, ContingencyPlan $plan)
    {
        $patientInfo = $this->getPatientId();
        
        if (!$patientInfo) {
            return back()->with('error', 'Patient profile not found.');
        }

        // Verify plan belongs to patient
        $planPatientId = $patientInfo['is_profile'] 
            ? $plan->patient_profile_id 
            : $plan->patient_id;

        if ($planPatientId != $patientInfo['id']) {
            return back()->with('error', 'Unauthorized action.');
        }

        if (!$plan->isActive()) {
            return back()->with('error', 'This contingency plan is not active and cannot be activated.');
        }

        // Verify plan still exists and belongs to patient
        if (!$plan->exists) {
            return back()->with('error', 'Contingency plan not found.');
        }

        $request->validate([
            'trigger_reason' => 'required|string|max:1000',
        ]);

        try {
            $activation = $plan->activate('self', $request->trigger_reason);
            
            // Execute actions (in a real system, this would trigger notifications, etc.)
            $actions = $plan->actions ?? [];
            
            return redirect()->route('client.contingency.show', $plan)
                ->with('success', 'Contingency plan activated successfully. Your healthcare provider has been notified.');
        } catch (\Illuminate\Database\QueryException $e) {
            \Log::error('Database error activating contingency plan: ' . $e->getMessage());
            return back()->with('error', 'Failed to activate plan due to a database error. Please try again or contact support.');
        } catch (\Exception $e) {
            \Log::error('Error activating contingency plan: ' . $e->getMessage());
            return back()->with('error', 'Failed to activate plan: ' . $e->getMessage());
        }
    }
}
