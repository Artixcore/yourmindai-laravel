<?php

namespace App\Http\Controllers;

use App\Models\PatientProfile;
use App\Models\Patient;
use App\Models\PsychometricAssessment;
use App\Models\PsychometricReport;
use Illuminate\Http\Request;

class ClientPsychometricController extends Controller
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
     * Display a listing of psychometric assessments.
     */
    public function index()
    {
        $patientInfo = $this->getPatientId();
        
        if (!$patientInfo) {
            return redirect()->route('client.dashboard')
                ->with('error', 'Patient profile not found.');
        }

        $pendingAssessments = PsychometricAssessment::where(
            $patientInfo['is_profile'] ? 'patient_profile_id' : 'patient_id',
            $patientInfo['id']
        )
        ->where('status', 'pending')
        ->with('scale', 'assignedByDoctor')
        ->orderBy('created_at', 'desc')
        ->get();

        $completedAssessments = PsychometricAssessment::where(
            $patientInfo['is_profile'] ? 'patient_profile_id' : 'patient_id',
            $patientInfo['id']
        )
        ->where('status', 'completed')
        ->with('scale', 'assignedByDoctor')
        ->orderBy('completed_at', 'desc')
        ->get();

        return view('client.assessments.index', compact('pendingAssessments', 'completedAssessments'));
    }

    /**
     * Display the assessment form.
     */
    public function show(PsychometricAssessment $assessment)
    {
        $patientInfo = $this->getPatientId();
        
        if (!$patientInfo) {
            return redirect()->route('client.dashboard')
                ->with('error', 'Patient profile not found.');
        }

        // Verify assessment belongs to patient
        $assessmentPatientId = $patientInfo['is_profile'] 
            ? $assessment->patient_profile_id 
            : $assessment->patient_id;

        if ($assessmentPatientId != $patientInfo['id']) {
            return redirect()->route('client.assessments.index')
                ->with('error', 'Unauthorized access.');
        }

        if ($assessment->status === 'completed') {
            return view('client.assessments.result', compact('assessment'));
        }

        $assessment->load('scale', 'assignedByDoctor');

        return view('client.assessments.show', compact('assessment'));
    }

    /**
     * Complete the assessment.
     */
    public function complete(Request $request, PsychometricAssessment $assessment)
    {
        $patientInfo = $this->getPatientId();
        
        if (!$patientInfo) {
            return back()->with('error', 'Patient profile not found.');
        }

        // Verify assessment belongs to patient
        $assessmentPatientId = $patientInfo['is_profile'] 
            ? $assessment->patient_profile_id 
            : $assessment->patient_id;

        if ($assessmentPatientId != $patientInfo['id']) {
            return back()->with('error', 'Unauthorized action.');
        }

        if ($assessment->status === 'completed') {
            return back()->with('error', 'Assessment already completed.');
        }

        $request->validate([
            'responses' => 'required|array',
        ]);

        try {
            $assessment->complete($request->responses);
            
            return redirect()->route('client.assessments.show', $assessment)
                ->with('success', 'Assessment completed successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to complete assessment: ' . $e->getMessage());
        }
    }

    /**
     * Generate report for completed assessment.
     */
    public function generateReport(PsychometricAssessment $assessment)
    {
        $patientInfo = $this->getPatientId();
        if (!$patientInfo) {
            return back()->with('error', 'Patient profile not found.');
        }

        $assessmentPatientId = $patientInfo['is_profile']
            ? $assessment->patient_profile_id
            : $assessment->patient_id;
        if ($assessmentPatientId != $patientInfo['id']) {
            return back()->with('error', 'Unauthorized access.');
        }

        if ($assessment->status !== 'completed') {
            return back()->with('error', 'Assessment must be completed first.');
        }

        $existing = PsychometricReport::where('assessment_id', $assessment->id)->first();
        if ($existing) {
            return redirect()->route('client.assessments.report', $assessment)
                ->with('info', 'Report already exists.');
        }

        $summary = $this->buildReportSummary($assessment);
        PsychometricReport::create([
            'assessment_id' => $assessment->id,
            'patient_profile_id' => $patientInfo['is_profile'] ? $patientInfo['id'] : null,
            'patient_id' => $patientInfo['is_profile'] ? null : $patientInfo['id'],
            'summary' => $summary,
            'generated_at' => now(),
        ]);

        return redirect()->route('client.assessments.report', $assessment)
            ->with('success', 'Report generated successfully.');
    }

    /**
     * Show report for completed assessment.
     */
    public function showReport(PsychometricAssessment $assessment)
    {
        $patientInfo = $this->getPatientId();
        if (!$patientInfo) {
            return redirect()->route('client.dashboard')->with('error', 'Patient profile not found.');
        }

        $assessmentPatientId = $patientInfo['is_profile']
            ? $assessment->patient_profile_id
            : $assessment->patient_id;
        if ($assessmentPatientId != $patientInfo['id']) {
            return redirect()->route('client.assessments.index')->with('error', 'Unauthorized access.');
        }

        if ($assessment->status !== 'completed') {
            return redirect()->route('client.assessments.show', $assessment)
                ->with('error', 'Complete the assessment first.');
        }

        $report = PsychometricReport::where('assessment_id', $assessment->id)->first();

        return view('client.assessments.report', compact('assessment', 'report'));
    }

    private function buildReportSummary(PsychometricAssessment $assessment): string
    {
        $parts = [];
        $parts[] = 'Assessment: ' . ($assessment->scale->name ?? 'Psychometric Assessment');
        $parts[] = 'Completed: ' . ($assessment->completed_at?->format('F j, Y') ?? 'N/A');
        $parts[] = '';
        $parts[] = 'Total Score: ' . ($assessment->total_score ?? 'N/A');
        if ($assessment->interpretation) {
            $parts[] = 'Interpretation: ' . $assessment->interpretation;
        }
        if ($assessment->sub_scores && count($assessment->sub_scores) > 0) {
            $parts[] = '';
            $parts[] = 'Sub-scores:';
            foreach ($assessment->sub_scores as $name => $value) {
                $parts[] = "  - {$name}: {$value}";
            }
        }
        if ($assessment->responses && count($assessment->responses) > 0) {
            $parts[] = '';
            $parts[] = 'Response summary: ' . count($assessment->responses) . ' items completed.';
        }

        return implode("\n", $parts);
    }
}
