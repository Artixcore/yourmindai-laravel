<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GeneralAssessment;
use App\Models\GeneralAssessmentQuestion;
use App\Models\PatientProfile;
use App\Models\User;

class GeneralAssessmentController extends Controller
{
    /**
     * Show all general assessments for a patient.
     */
    public function index(Request $request, $patientId)
    {
        $patient = PatientProfile::with('user')->findOrFail($patientId);
        
        // Check authorization
        if (!$this->canAccessPatient($request->user(), $patient)) {
            abort(403);
        }

        $assessments = GeneralAssessment::where('patient_id', $patientId)
            ->with(['questions', 'responses', 'assignedByDoctor'])
            ->orderBy('assigned_at', 'desc')
            ->get();

        return view('doctor.patients.general-assessments.index', compact('patient', 'assessments'));
    }

    /**
     * Show form to create new assessment for patient.
     */
    public function create(Request $request, $patientId)
    {
        $patient = PatientProfile::with('user')->findOrFail($patientId);
        
        // Check authorization
        if (!$this->canAccessPatient($request->user(), $patient)) {
            abort(403);
        }

        return view('doctor.patients.general-assessments.create', compact('patient'));
    }

    /**
     * Store new assessment for patient.
     */
    public function store(Request $request, $patientId)
    {
        $patient = PatientProfile::findOrFail($patientId);
        
        // Check authorization
        if (!$this->canAccessPatient($request->user(), $patient)) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'questions' => 'required|array|min:1',
            'questions.*.question_text' => 'required|string',
            'questions.*.question_type' => 'required|in:text,multiple_choice,scale,yes_no',
            'questions.*.options' => 'nullable|array',
            'questions.*.is_required' => 'boolean',
        ]);

        // Create assessment
        $assessment = GeneralAssessment::create([
            'patient_id' => $patient->id,
            'assigned_by' => $request->user()->id,
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'status' => 'pending',
            'assigned_at' => now(),
        ]);

        // Create questions
        foreach ($validated['questions'] as $index => $questionData) {
            GeneralAssessmentQuestion::create([
                'assessment_id' => $assessment->id,
                'order' => $index,
                'question_text' => $questionData['question_text'],
                'question_type' => $questionData['question_type'],
                'options' => $questionData['options'] ?? null,
                'is_required' => $questionData['is_required'] ?? false,
            ]);
        }

        return redirect()->route('patients.general-assessments.index', $patient->id)
            ->with('success', 'General assessment created and assigned successfully!');
    }

    /**
     * Show specific assessment details.
     */
    public function show(Request $request, $patientId, $assessmentId)
    {
        $patient = PatientProfile::with('user')->findOrFail($patientId);
        $assessment = GeneralAssessment::where('id', $assessmentId)
            ->where('patient_id', $patientId)
            ->with(['questions', 'responses', 'feedback'])
            ->firstOrFail();

        // Check authorization
        if (!$this->canAccessPatient($request->user(), $patient)) {
            abort(403);
        }

        return view('doctor.patients.general-assessments.show', compact('patient', 'assessment'));
    }

    /**
     * Check if doctor can access this patient.
     */
    private function canAccessPatient($user, $patient)
    {
        return $user->isAdmin() || $user->id === $patient->doctor_id;
    }
}
