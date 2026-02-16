<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GeneralAssessment;
use App\Models\GeneralAssessmentQuestion;
use App\Models\Patient;
use App\Models\PatientProfile;
use App\Models\User;

class GeneralAssessmentController extends Controller
{
    private function resolvePatientProfile(Patient $patient): PatientProfile
    {
        $profile = $patient->resolvePatientProfile();
        if (!$profile) {
            abort(404, 'Patient profile not found for this patient.');
        }
        return $profile;
    }

    /**
     * Show all general assessments for a patient.
     */
    public function index(Request $request, Patient $patient)
    {
        $patientProfile = $this->resolvePatientProfile($patient);
        
        // Check authorization
        if (!$this->canAccessPatient($request->user(), $patientProfile)) {
            abort(403);
        }

        $assessments = GeneralAssessment::where('patient_id', $patientProfile->id)
            ->with(['questions', 'responses', 'assignedByDoctor'])
            ->orderBy('assigned_at', 'desc')
            ->get();

        return view('doctor.patients.general-assessments.index', compact('patient', 'patientProfile', 'assessments'));
    }

    /**
     * Show form to create new assessment for patient.
     */
    public function create(Request $request, Patient $patient)
    {
        $patientProfile = $this->resolvePatientProfile($patient);
        
        // Check authorization
        if (!$this->canAccessPatient($request->user(), $patientProfile)) {
            abort(403);
        }

        return view('doctor.patients.general-assessments.create', compact('patient', 'patientProfile'));
    }

    /**
     * Store new assessment for patient.
     */
    public function store(Request $request, Patient $patient)
    {
        $patientProfile = $this->resolvePatientProfile($patient);
        
        // Check authorization
        if (!$this->canAccessPatient($request->user(), $patientProfile)) {
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
            'patient_id' => $patientProfile->id,
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

        return redirect()->route('patients.general-assessments.index', $patient)
            ->with('success', 'General assessment created and assigned successfully!');
    }

    /**
     * Show specific assessment details.
     */
    public function show(Request $request, Patient $patient, $assessmentId)
    {
        $patientProfile = $this->resolvePatientProfile($patient);
        $assessment = GeneralAssessment::where('id', $assessmentId)
            ->where('patient_id', $patientProfile->id)
            ->with(['questions', 'responses', 'feedback'])
            ->firstOrFail();

        // Check authorization
        if (!$this->canAccessPatient($request->user(), $patientProfile)) {
            abort(403);
        }

        return view('doctor.patients.general-assessments.show', compact('patient', 'patientProfile', 'assessment'));
    }

    /**
     * Check if doctor can access this patient.
     */
    private function canAccessPatient($user, $patient)
    {
        return $user->isAdmin() || $user->id === $patient->doctor_id;
    }
}
