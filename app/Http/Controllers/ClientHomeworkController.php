<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HomeworkAssignment;
use App\Models\HomeworkCompletion;
use App\Models\PatientProfile;

class ClientHomeworkController extends Controller
{
    /**
     * Show all homework assignments for the authenticated client.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $patient = PatientProfile::where('user_id', $user->id)->firstOrFail();

        // Get all active homework assignments grouped by type
        $homework = HomeworkAssignment::where('patient_id', $patient->id)
            ->whereIn('status', ['assigned', 'in_progress'])
            ->with(['feedback', 'practiceProgressions', 'completions'])
            ->get()
            ->groupBy('homework_type');

        // Get completion stats
        $stats = [
            'total_assigned' => HomeworkAssignment::where('patient_id', $patient->id)
                ->whereIn('status', ['assigned', 'in_progress'])
                ->count(),
            'completed_today' => HomeworkCompletion::where('patient_id', $patient->id)
                ->whereDate('completion_date', today())
                ->where('is_completed', true)
                ->count(),
            'completion_percentage' => $this->getCompletionPercentage($patient->id),
        ];

        return view('client.homework.index', compact('homework', 'stats'));
    }

    /**
     * Show specific homework assignment details.
     */
    public function show(Request $request, $homeworkId)
    {
        $user = $request->user();
        $patient = PatientProfile::where('user_id', $user->id)->firstOrFail();

        $homework = HomeworkAssignment::where('id', $homeworkId)
            ->where('patient_id', $patient->id)
            ->with([
                'feedback',
                'practiceProgressions' => function ($query) {
                    $query->orderBy('progress_date', 'desc');
                },
                'completions' => function ($query) {
                    $query->orderBy('completion_date', 'desc');
                },
                'assignedByDoctor'
            ])
            ->firstOrFail();

        // Get today's completion if it exists
        $todayCompletion = $homework->completions()
            ->whereDate('completion_date', today())
            ->first();

        return view('client.homework.show', compact('homework', 'todayCompletion'));
    }

    /**
     * Mark homework as completed for today.
     */
    public function complete(Request $request, $homeworkId)
    {
        $user = $request->user();
        $patient = PatientProfile::where('user_id', $user->id)->firstOrFail();

        $homework = HomeworkAssignment::where('id', $homeworkId)
            ->where('patient_id', $patient->id)
            ->firstOrFail();

        $validated = $request->validate([
            'completion_percentage' => 'required|integer|min:0|max:100',
            'patient_notes' => 'nullable|string',
            'completion_data' => 'nullable|array',
        ]);

        // Create completion record
        $completion = HomeworkCompletion::updateOrCreate(
            [
                'homework_assignment_id' => $homework->id,
                'patient_id' => $patient->id,
                'completion_date' => today(),
            ],
            [
                'completion_time' => now()->format('H:i:s'),
                'is_completed' => $validated['completion_percentage'] >= 100,
                'completion_percentage' => $validated['completion_percentage'],
                'patient_notes' => $validated['patient_notes'] ?? null,
                'completion_data' => $validated['completion_data'] ?? null,
            ]
        );

        // Add self-feedback and progression
        $homework->addFeedback(
            $patient->id,
            'self',
            $user->id,
            $validated['patient_notes'] ?? 'Completed',
            null,
            $validated['completion_data'] ?? null
        );

        $homework->addProgression(
            $patient->id,
            today()->format('Y-m-d'),
            $validated['completion_percentage'],
            $validated['completion_percentage'] >= 100 ? 'completed' : 'in_progress',
            'self',
            $user->id,
            $validated['patient_notes'] ?? null
        );

        // Update homework status
        if ($validated['completion_percentage'] >= 100 && $homework->frequency === 'as_needed') {
            $homework->update(['status' => 'completed']);
        } else {
            $homework->update(['status' => 'in_progress']);
        }

        return redirect()->route('client.homework.show', $homework->id)
            ->with('success', 'Homework marked as completed!');
    }

    /**
     * Get completion percentage for patient.
     */
    private function getCompletionPercentage($patientId)
    {
        $totalAssigned = HomeworkAssignment::where('patient_id', $patientId)
            ->whereIn('status', ['assigned', 'in_progress', 'completed'])
            ->count();

        if ($totalAssigned === 0) {
            return 0;
        }

        $completed = HomeworkAssignment::where('patient_id', $patientId)
            ->where('status', 'completed')
            ->count();

        return round(($completed / $totalAssigned) * 100);
    }
}
