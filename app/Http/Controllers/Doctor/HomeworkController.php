<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreHomeworkRequest;
use App\Http\Requests\UpdateHomeworkRequest;
use Illuminate\Http\Request;
use App\Models\HomeworkAssignment;
use App\Models\HomeworkCompletion;
use App\Models\HomeworkMedia;
use App\Models\Patient;
use App\Models\PatientProfile;
use App\Models\Session;
use App\Models\User;
use App\Notifications\HomeworkAssignedNotification;
use App\Notifications\HomeworkReviewedNotification;
use Illuminate\Support\Facades\DB;

class HomeworkController extends Controller
{
    /**
     * Show all homework for a patient.
     */
    public function index(Request $request, Patient $patient)
    {
        $patientProfile = $this->resolvePatientProfile($patient);
        
        // Check authorization
        if (!$this->canAccessPatient($request->user(), $patientProfile)) {
            abort(403);
        }

        $homework = HomeworkAssignment::where('patient_id', $patientProfile->id)
            ->with(['assignedByDoctor', 'session', 'completions', 'feedback', 'practiceProgressions'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('doctor.patients.homework.index', compact('patient', 'patientProfile', 'homework'));
    }

    /**
     * Resolve Patient to PatientProfile for models that use patient_profile_id.
     */
    private function resolvePatientProfile(Patient $patient): PatientProfile
    {
        $profile = $patient->resolvePatientProfile();
        if (!$profile) {
            abort(404, 'Patient profile not found for this patient.');
        }
        return $profile;
    }

    /**
     * Show form to assign homework to patient.
     */
    public function create(Request $request, Patient $patient)
    {
        $patientProfile = $this->resolvePatientProfile($patient);
        
        // Check authorization
        if (!$this->canAccessPatient($request->user(), $patientProfile)) {
            abort(403);
        }

        // Get recent sessions for this patient (Session uses Patient)
        $sessions = Session::where('patient_id', $patient->id)
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

        return view('doctor.patients.homework.create', compact('patient', 'patientProfile', 'sessions', 'homeworkTypes'));
    }

    /**
     * Store homework assignment for patient.
     */
    public function store(StoreHomeworkRequest $request, Patient $patient)
    {
        $patientProfile = $this->resolvePatientProfile($patient);
        
        // Check authorization
        if (!$this->canAccessPatient($request->user(), $patientProfile)) {
            abort(403);
        }

        $validated = $request->validated();

        $frequencyValue = null;
        if (!empty($validated['frequency_value'])) {
            $frequencyValue = is_array($validated['frequency_value']) ? $validated['frequency_value'] : ['value' => $validated['frequency_value']];
        }

        $homework = null;
        DB::transaction(function () use ($request, $patientProfile, $validated, $frequencyValue, &$homework) {
            $homework = HomeworkAssignment::create([
                'patient_id' => $patientProfile->id,
                'assigned_by' => $request->user()->id,
                'session_id' => $validated['session_id'] ?? null,
                'homework_type' => $validated['homework_type'],
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'instructions' => $validated['instructions'] ?? null,
                'goals' => $validated['goals'] ?? null,
                'frequency' => $validated['frequency'],
                'frequency_type' => $validated['frequency_type'] ?? null,
                'frequency_value' => $frequencyValue,
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'] ?? null,
                'reminder_at' => isset($validated['reminder_at']) ? $validated['reminder_at'] : null,
                'status' => 'assigned',
                'requires_parent_feedback' => $validated['requires_parent_feedback'] ?? false,
                'requires_others_feedback' => $validated['requires_others_feedback'] ?? false,
            ] + ($validated['homework_type'] === 'contingency' ? [
                'contingency_self_action_points' => isset($validated['contingency_self_action_points']) ? (int) $validated['contingency_self_action_points'] : null,
                'contingency_others_help_points' => isset($validated['contingency_others_help_points']) ? (int) $validated['contingency_others_help_points'] : null,
                'contingency_not_working_points' => isset($validated['contingency_not_working_points']) ? (int) $validated['contingency_not_working_points'] : null,
            ] : []));

            if (!empty($validated['media'])) {
                foreach ($validated['media'] as $mediaItem) {
                    if (!empty($mediaItem['url'])) {
                        HomeworkMedia::create([
                            'homework_assignment_id' => $homework->id,
                            'type' => $mediaItem['type'],
                            'url' => $mediaItem['url'],
                            'title' => $mediaItem['title'] ?? null,
                        ]);
                    }
                }
            }
        });

        if ($homework) {
            $clientUser = $patientProfile->user;
            if ($clientUser) {
                $clientUser->notify(new HomeworkAssignedNotification(
                    $homework,
                    'New Homework Assigned',
                    "New homework \"{$homework->title}\" has been assigned to you.",
                    route('client.homework.show', $homework->id)
                ));
            }
        }

        return redirect()->route('patients.homework.index', $patient)
            ->with('success', 'Homework assigned successfully!');
    }

    /**
     * Show specific homework details.
     */
    public function show(Request $request, Patient $patient, $homeworkId)
    {
        $patientProfile = $this->resolvePatientProfile($patient);
        $homework = HomeworkAssignment::where('id', $homeworkId)
            ->where('patient_id', $patientProfile->id)
            ->with(['completions' => fn ($q) => $q->with('reviewer'), 'feedback', 'practiceProgressions', 'session'])
            ->firstOrFail();

        // Check authorization
        if (!$this->canAccessPatient($request->user(), $patientProfile)) {
            abort(403);
        }

        return view('doctor.patients.homework.show', compact('patient', 'patientProfile', 'homework'));
    }

    /**
     * Update homework assignment.
     */
    public function update(UpdateHomeworkRequest $request, Patient $patient, $homeworkId)
    {
        $patientProfile = $this->resolvePatientProfile($patient);
        $homework = HomeworkAssignment::where('id', $homeworkId)
            ->where('patient_id', $patientProfile->id)
            ->firstOrFail();

        // Check authorization
        if (!$this->canAccessPatient($request->user(), $patientProfile)) {
            abort(403);
        }

        $validated = $request->validated();

        $homework->update($validated);

        if ($request->ajax() || $request->wantsJson()) {
            $homework->refresh();
            return response()->json([
                'success' => true,
                'message' => 'Homework updated successfully!',
                'html' => view('partials.homework_status_row', compact('homework'))->render(),
                'target' => '#homework-status-row',
            ]);
        }

        return redirect()->route('patients.homework.show', [$patient, $homework])
            ->with('success', 'Homework updated successfully!');
    }

    /**
     * Review a homework completion (mark reviewed, optionally override score).
     */
    public function reviewCompletion(Request $request, Patient $patient, $homeworkId, $completionId)
    {
        $patientProfile = $this->resolvePatientProfile($patient);
        $homework = HomeworkAssignment::where('id', $homeworkId)
            ->where('patient_id', $patientProfile->id)
            ->firstOrFail();
        $completion = HomeworkCompletion::where('id', $completionId)
            ->where('homework_assignment_id', $homework->id)
            ->firstOrFail();

        if (!$this->canAccessPatient($request->user(), $patientProfile)) {
            abort(403);
        }

        $validated = $request->validate([
            'score_value' => 'nullable|integer|in:-10,5,10',
            'reviewer_note' => 'nullable|string|max:500',
        ]);

        $completion->update([
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
            'score_value' => $validated['score_value'] ?? $completion->score_value,
        ]);

        $clientUser = $patientProfile->user ?? null;
        if ($clientUser) {
            $clientUser->notify(new HomeworkReviewedNotification(
                $homework,
                $completion,
                'Homework Reviewed',
                "Your homework \"{$homework->title}\" has been reviewed by your doctor.",
                route('client.homework.show', $homework->id)
            ));
        }

        if ($request->ajax() || $request->wantsJson()) {
            $completion->load('reviewer');
            return response()->json([
                'success' => true,
                'message' => 'Completion reviewed successfully!',
                'html' => view('partials.homework_completion_row', compact('patient', 'homework', 'completion'))->render(),
                'target' => '#completion-' . $completion->id,
                'replace' => true,
            ]);
        }

        return redirect()->route('patients.homework.show', [$patient, $homework])
            ->with('success', 'Completion reviewed successfully!');
    }

    /**
     * Check if doctor can access this patient.
     */
    private function canAccessPatient($user, $patient)
    {
        return $user->isAdmin() || $user->id === $patient->doctor_id;
    }
}
