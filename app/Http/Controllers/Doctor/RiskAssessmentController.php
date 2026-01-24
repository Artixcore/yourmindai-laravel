<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RiskAssessment;
use App\Models\PatientProfile;
use App\Models\User;

class RiskAssessmentController extends Controller
{
    public function index(Request $request, PatientProfile $patient)
    {
        $user = $request->user();
        
        if (!$this->canAccessPatient($user, $patient)) {
            abort(403, 'Unauthorized access to patient data');
        }

        $assessments = RiskAssessment::where('patient_id', $patient->id)
            ->with('assessedBy')
            ->orderBy('assessment_date', 'desc')
            ->paginate(15);

        // Calculate statistics
        $stats = [
            'total' => RiskAssessment::where('patient_id', $patient->id)->count(),
            'high_risk' => RiskAssessment::where('patient_id', $patient->id)->whereIn('risk_level', ['high', 'critical'])->count(),
            'latest_level' => $assessments->first()?->risk_level ?? 'none',
            'avg_level' => $this->calculateAverageRiskLevel($patient->id),
        ];

        return view('doctor.patients.risk-assessments.index', compact('patient', 'assessments', 'stats'));
    }

    public function create(Request $request, PatientProfile $patient)
    {
        $user = $request->user();
        
        if (!$this->canAccessPatient($user, $patient)) {
            abort(403, 'Unauthorized access to patient data');
        }

        $riskFactors = [
            'suicidal_ideation' => 'Suicidal Ideation',
            'self_harm' => 'Self Harm',
            'substance_abuse' => 'Substance Abuse',
            'aggression' => 'Aggression towards Others',
            'psychotic_symptoms' => 'Psychotic Symptoms',
            'severe_depression' => 'Severe Depression',
            'severe_anxiety' => 'Severe Anxiety',
            'eating_disorder' => 'Eating Disorder',
            'trauma_symptoms' => 'Trauma Symptoms',
            'medication_noncompliance' => 'Medication Non-compliance',
            'isolation' => 'Social Isolation',
            'family_conflict' => 'Family Conflict',
            'financial_stress' => 'Financial Stress',
            'legal_issues' => 'Legal Issues',
            'housing_instability' => 'Housing Instability',
        ];

        return view('doctor.patients.risk-assessments.create', compact('patient', 'riskFactors'));
    }

    public function store(Request $request, PatientProfile $patient)
    {
        $user = $request->user();
        
        if (!$this->canAccessPatient($user, $patient)) {
            abort(403, 'Unauthorized access to patient data');
        }

        $validated = $request->validate([
            'assessment_date' => 'required|date',
            'risk_level' => 'required|in:none,low,moderate,high,critical',
            'risk_factors' => 'nullable|array',
            'assessment_notes' => 'nullable|string',
            'intervention_plan' => 'nullable|string',
        ]);

        $validated['patient_id'] = $patient->id;
        $validated['assessed_by'] = $user->id;

        // Auto-send alert for high/critical risk
        if (in_array($validated['risk_level'], ['high', 'critical'])) {
            $validated['alert_sent'] = true;
            $validated['alert_sent_at'] = now();
        }

        $assessment = RiskAssessment::create($validated);

        return redirect()
            ->route('doctor.patients.risk-assessments.show', [$patient, $assessment])
            ->with('success', 'Risk assessment created successfully!' . 
                ($assessment->isHighRisk() ? ' Alert has been sent due to high risk level.' : ''));
    }

    public function show(Request $request, PatientProfile $patient, RiskAssessment $assessment)
    {
        $user = $request->user();
        
        if (!$this->canAccessPatient($user, $patient)) {
            abort(403, 'Unauthorized access to patient data');
        }

        if ($assessment->patient_id !== $patient->id) {
            abort(404, 'Assessment not found for this patient');
        }

        $assessment->load('assessedBy', 'feedback', 'practiceProgressions');

        // Get previous assessment for comparison
        $previousAssessment = RiskAssessment::where('patient_id', $patient->id)
            ->where('assessment_date', '<', $assessment->assessment_date)
            ->orderBy('assessment_date', 'desc')
            ->first();

        return view('doctor.patients.risk-assessments.show', compact('patient', 'assessment', 'previousAssessment'));
    }

    public function update(Request $request, PatientProfile $patient, RiskAssessment $assessment)
    {
        $user = $request->user();
        
        if (!$this->canAccessPatient($user, $patient)) {
            abort(403, 'Unauthorized access to patient data');
        }

        if ($assessment->patient_id !== $patient->id) {
            abort(404, 'Assessment not found for this patient');
        }

        $validated = $request->validate([
            'risk_level' => 'required|in:none,low,moderate,high,critical',
            'risk_factors' => 'nullable|array',
            'assessment_notes' => 'nullable|string',
            'intervention_plan' => 'nullable|string',
        ]);

        // Update alert if risk level changed to high/critical
        if (in_array($validated['risk_level'], ['high', 'critical']) && !$assessment->alert_sent) {
            $validated['alert_sent'] = true;
            $validated['alert_sent_at'] = now();
        }

        $assessment->update($validated);

        return redirect()
            ->route('doctor.patients.risk-assessments.show', [$patient, $assessment])
            ->with('success', 'Risk assessment updated successfully!');
    }

    // Authorization helper
    private function canAccessPatient(User $user, PatientProfile $patient): bool
    {
        // Admin can access all patients
        if ($user->isAdmin()) {
            return true;
        }

        // Doctor can access own patients
        return $user->id === $patient->doctor_id;
    }

    // Helper method to calculate average risk level
    private function calculateAverageRiskLevel($patientId)
    {
        $riskLevels = ['none' => 0, 'low' => 1, 'moderate' => 2, 'high' => 3, 'critical' => 4];
        
        $assessments = RiskAssessment::where('patient_id', $patientId)
            ->orderBy('assessment_date', 'desc')
            ->take(5)
            ->get();

        if ($assessments->isEmpty()) {
            return 'none';
        }

        $sum = $assessments->sum(function($assessment) use ($riskLevels) {
            return $riskLevels[$assessment->risk_level] ?? 0;
        });

        $avg = $sum / $assessments->count();

        foreach ($riskLevels as $level => $value) {
            if ($avg <= $value + 0.5) {
                return $level;
            }
        }

        return 'critical';
    }
}
