<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GeneralAssessment;
use App\Models\PatientProfile;
use App\Models\GeneralAssessmentResponse;

class ClientGeneralAssessmentController extends Controller
{
    /**
     * Show all general assessments for the authenticated client.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $patient = PatientProfile::where('user_id', $user->id)->firstOrFail();

        $assessments = GeneralAssessment::where('patient_id', $patient->id)
            ->with(['assignedByDoctor', 'questions', 'responses'])
            ->orderBy('assigned_at', 'desc')
            ->get();

        $stats = [
            'pending' => $assessments->where('status', 'pending')->count(),
            'in_progress' => $assessments->where('status', 'in_progress')->count(),
            'completed' => $assessments->where('status', 'completed')->count(),
        ];

        return view('client.general-assessment.index', compact('assessments', 'stats'));
    }

    /**
     * Show specific assessment and take it.
     */
    public function show(Request $request, $assessmentId)
    {
        $user = $request->user();
        $patient = PatientProfile::where('user_id', $user->id)->firstOrFail();

        $assessment = GeneralAssessment::where('id', $assessmentId)
            ->where('patient_id', $patient->id)
            ->with(['questions', 'responses', 'assignedByDoctor'])
            ->firstOrFail();

        // Mark as started if pending
        if ($assessment->status === 'pending') {
            $assessment->update([
                'status' => 'in_progress',
                'started_at' => now(),
            ]);
        }

        return view('client.general-assessment.show', compact('assessment'));
    }

    /**
     * Submit assessment responses.
     */
    public function submit(Request $request, $assessmentId)
    {
        $user = $request->user();
        $patient = PatientProfile::where('user_id', $user->id)->firstOrFail();

        $assessment = GeneralAssessment::where('id', $assessmentId)
            ->where('patient_id', $patient->id)
            ->with('questions')
            ->firstOrFail();

        $validated = $request->validate([
            'responses' => 'required|array',
            'responses.*' => 'required',
        ]);

        // Save responses
        foreach ($validated['responses'] as $questionId => $responseData) {
            GeneralAssessmentResponse::updateOrCreate(
                [
                    'assessment_id' => $assessment->id,
                    'question_id' => $questionId,
                    'patient_id' => $patient->id,
                ],
                [
                    'response_text' => is_array($responseData) ? json_encode($responseData) : $responseData,
                    'response_score' => is_numeric($responseData) ? $responseData : null,
                ]
            );
        }

        // Mark as completed
        $assessment->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        // Add self-feedback
        $assessment->addFeedback(
            $patient->id,
            'self',
            $user->id,
            'Assessment completed'
        );

        return redirect()->route('client.general-assessment.result', $assessment->id)
            ->with('success', 'Assessment completed successfully!');
    }

    /**
     * Show assessment results.
     */
    public function result(Request $request, $assessmentId)
    {
        $user = $request->user();
        $patient = PatientProfile::where('user_id', $user->id)->firstOrFail();

        $assessment = GeneralAssessment::where('id', $assessmentId)
            ->where('patient_id', $patient->id)
            ->where('status', 'completed')
            ->with(['questions', 'responses', 'feedback'])
            ->firstOrFail();

        return view('client.general-assessment.result', compact('assessment'));
    }
}
