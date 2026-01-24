<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HomeworkAssignment;
use App\Models\PatientProfile;
use App\Models\Session;

class HomeworkController extends Controller
{
    /**
     * Show all homework for a patient.
     */
    public function index(Request $request, $patientId)
    {
        $patient = PatientProfile::with('user')->findOrFail($patientId);
        
        // Check authorization
        if (!$this->canAccessPatient($request->user(), $patient)) {
            abort(403);
        }

        $homework = HomeworkAssignment::where('patient_id', $patientId)
            ->with(['assignedByDoctor', 'session', 'completions', 'feedback', 'practiceProgressions'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('doctor.patients.homework.index', compact('patient', 'homework'));
    }

    /**
     * Show form to assign homework to patient.
     */
    public function create(Request $request, $patientId)
    {
        $patient = PatientProfile::with('user')->findOrFail($patientId);
        
        // Check authorization
        if (!$this->canAccessPatient($request->user(), $patient)) {
            abort(403);
        }

        // Get recent sessions for this patient
        $sessions = Session::where('patient_id', $patientId)
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        // Homework types
        $homeworkTypes = [
            'psychotherapy' => 'Psychotherapy',
            'lifestyle_modification' => 'Lifestyle Modification',
            'sleep_tracking' => 'Sleep Tracking',
            'mood_tracking' => 'Mood Tracking',
            'personal_journal' => 'Personal Journal',
            'risk_tracking' => 'Risk Tracking',
            'contingency' => 'Contingency Planning',
            'exercise' => 'Exercise',
            'parent_role' => 'Parent\'s Role',
            'others_role' => 'Others\' Role',
            'self_help_tools' => 'Self-Help Tools',
        ];

        return view('doctor.patients.homework.create', compact('patient', 'sessions', 'homeworkTypes'));
    }

    /**
     * Store homework assignment for patient.
     */
    public function store(Request $request, $patientId)
    {
        $patient = PatientProfile::findOrFail($patientId);
        
        // Check authorization
        if (!$this->canAccessPatient($request->user(), $patient)) {
            abort(403);
        }

        $validated = $request->validate([
            'session_id' => 'nullable|exists:sessions,id',
            'homework_type' => 'required|in:psychotherapy,lifestyle_modification,sleep_tracking,mood_tracking,personal_journal,risk_tracking,contingency,exercise,parent_role,others_role,self_help_tools',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'instructions' => 'nullable|string',
            'goals' => 'nullable|array',
            'frequency' => 'required|in:daily,weekly,as_needed',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'requires_parent_feedback' => 'boolean',
            'requires_others_feedback' => 'boolean',
        ]);

        $homework = HomeworkAssignment::create([
            'patient_id' => $patient->id,
            'assigned_by' => $request->user()->id,
            'session_id' => $validated['session_id'] ?? null,
            'homework_type' => $validated['homework_type'],
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'instructions' => $validated['instructions'] ?? null,
            'goals' => $validated['goals'] ?? null,
            'frequency' => $validated['frequency'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'] ?? null,
            'status' => 'assigned',
            'requires_parent_feedback' => $validated['requires_parent_feedback'] ?? false,
            'requires_others_feedback' => $validated['requires_others_feedback'] ?? false,
        ]);

        return redirect()->route('patients.homework.index', $patient->id)
            ->with('success', 'Homework assigned successfully!');
    }

    /**
     * Show specific homework details.
     */
    public function show(Request $request, $patientId, $homeworkId)
    {
        $patient = PatientProfile::with('user')->findOrFail($patientId);
        $homework = HomeworkAssignment::where('id', $homeworkId)
            ->where('patient_id', $patientId)
            ->with(['completions', 'feedback', 'practiceProgressions', 'session'])
            ->firstOrFail();

        // Check authorization
        if (!$this->canAccessPatient($request->user(), $patient)) {
            abort(403);
        }

        return view('doctor.patients.homework.show', compact('patient', 'homework'));
    }

    /**
     * Update homework assignment.
     */
    public function update(Request $request, $patientId, $homeworkId)
    {
        $patient = PatientProfile::findOrFail($patientId);
        $homework = HomeworkAssignment::where('id', $homeworkId)
            ->where('patient_id', $patientId)
            ->firstOrFail();

        // Check authorization
        if (!$this->canAccessPatient($request->user(), $patient)) {
            abort(403);
        }

        $validated = $request->validate([
            'status' => 'required|in:assigned,in_progress,completed,cancelled',
            'end_date' => 'nullable|date',
        ]);

        $homework->update($validated);

        return redirect()->route('patients.homework.show', [$patient->id, $homework->id])
            ->with('success', 'Homework updated successfully!');
    }

    /**
     * Check if doctor can access this patient.
     */
    private function canAccessPatient($user, $patient)
    {
        return $user->isAdmin() || $user->id === $patient->doctor_id;
    }
}
