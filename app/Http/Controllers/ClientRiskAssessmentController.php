<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RiskAssessment;
use App\Models\PatientProfile;

class ClientRiskAssessmentController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        
        // Get patient profile
        $patient = PatientProfile::where('user_id', $user->id)->first();
        
        if (!$patient) {
            abort(404, 'Patient profile not found');
        }

        // Get all risk assessments for this patient
        $assessments = RiskAssessment::where('patient_id', $patient->id)
            ->with('assessedBy')
            ->orderBy('assessment_date', 'desc')
            ->paginate(10);

        // Calculate statistics
        $stats = [
            'total' => RiskAssessment::where('patient_id', $patient->id)->count(),
            'latest_level' => $assessments->first()?->risk_level ?? 'none',
            'has_intervention' => $assessments->first()?->intervention_plan ? true : false,
        ];

        return view('client.risk-assessment.index', compact('assessments', 'stats', 'patient'));
    }

    public function show(Request $request, RiskAssessment $assessment)
    {
        $user = $request->user();
        
        // Get patient profile
        $patient = PatientProfile::where('user_id', $user->id)->first();
        
        if (!$patient) {
            abort(404, 'Patient profile not found');
        }

        // Ensure the assessment belongs to this patient
        if ($assessment->patient_id !== $patient->id) {
            abort(403, 'Unauthorized access to this assessment');
        }

        $assessment->load('assessedBy');

        return view('client.risk-assessment.show', compact('assessment', 'patient'));
    }
}
