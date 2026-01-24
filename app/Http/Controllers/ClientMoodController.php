<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MoodLog;
use App\Models\PatientProfile;
use App\Models\HomeworkAssignment;
use Carbon\Carbon;

class ClientMoodController extends Controller
{
    /**
     * Show mood tracking interface.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $patient = PatientProfile::where('user_id', $user->id)->firstOrFail();

        // Get mood logs for last 30 days
        $startDate = Carbon::now()->subDays(30);
        $moodLogs = MoodLog::where('patient_id', $patient->id)
            ->where('log_date', '>=', $startDate)
            ->orderBy('log_date', 'desc')
            ->orderBy('log_time', 'desc')
            ->with(['feedback', 'practiceProgressions'])
            ->get();

        // Get mood tracking homework assignment if exists
        $moodHomework = HomeworkAssignment::where('patient_id', $patient->id)
            ->where('homework_type', 'mood_tracking')
            ->whereIn('status', ['assigned', 'in_progress'])
            ->first();

        // Calculate stats
        $stats = [
            'total_entries' => $moodLogs->count(),
            'avg_mood' => round($moodLogs->avg('mood_rating'), 1),
            'current_streak' => $this->getCurrentStreak($patient->id),
        ];

        return view('client.mood.index', compact('moodLogs', 'moodHomework', 'stats'));
    }

    /**
     * Store new mood log entry.
     */
    public function store(Request $request)
    {
        $user = $request->user();
        $patient = PatientProfile::where('user_id', $user->id)->firstOrFail();

        $validated = $request->validate([
            'log_date' => 'required|date',
            'log_time' => 'nullable|date_format:H:i',
            'mood_rating' => 'required|integer|min:1|max:10',
            'mood_emoji' => 'nullable|string|max:10',
            'notes' => 'nullable|string',
            'triggers' => 'nullable|array',
            'activities' => 'nullable|array',
            'homework_assignment_id' => 'nullable|exists:homework_assignments,id',
        ]);

        $moodLog = MoodLog::create([
            'patient_id' => $patient->id,
            'homework_assignment_id' => $validated['homework_assignment_id'] ?? null,
            'log_date' => $validated['log_date'],
            'log_time' => $validated['log_time'] ?? now()->format('H:i:s'),
            'mood_rating' => $validated['mood_rating'],
            'mood_emoji' => $validated['mood_emoji'] ?? $this->getMoodEmoji($validated['mood_rating']),
            'notes' => $validated['notes'] ?? null,
            'triggers' => $validated['triggers'] ?? null,
            'activities' => $validated['activities'] ?? null,
        ]);

        // Add self-feedback
        $moodLog->addFeedback(
            $patient->id,
            'self',
            $user->id,
            $validated['notes'] ?? 'Mood logged',
            $validated['mood_rating']
        );

        // Add progression if linked to homework
        if ($validated['homework_assignment_id'] ?? null) {
            $homework = HomeworkAssignment::find($validated['homework_assignment_id']);
            $homework->addProgression(
                $patient->id,
                $validated['log_date'],
                100,
                'completed',
                'self',
                $user->id
            );
        }

        return redirect()->route('client.mood.index')
            ->with('success', 'Mood logged successfully!');
    }

    /**
     * Get current streak of consecutive days logged.
     */
    private function getCurrentStreak($patientId)
    {
        $streak = 0;
        $currentDate = Carbon::today();
        
        while (MoodLog::where('patient_id', $patientId)
            ->whereDate('log_date', $currentDate)
            ->exists()) {
            $streak++;
            $currentDate = $currentDate->subDay();
        }
        
        return $streak;
    }

    /**
     * Get emoji based on mood rating.
     */
    private function getMoodEmoji($rating)
    {
        return match(true) {
            $rating <= 2 => '😢',
            $rating <= 4 => '😕',
            $rating <= 6 => '😐',
            $rating <= 8 => '🙂',
            default => '😊',
        };
    }
}
