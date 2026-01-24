<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Routine;
use App\Models\RoutineItem;
use App\Models\RoutineLog;
use App\Models\PatientProfile;
use Carbon\Carbon;

class ClientRoutineController extends Controller
{
    /**
     * Show daily routine checklist.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $patient = PatientProfile::where('user_id', $user->id)->firstOrFail();

        // Get active routine
        $routine = Routine::where('patient_id', $patient->id)
            ->where('is_active', true)
            ->with(['items' => function($query) {
                $query->orderBy('order');
            }])
            ->first();

        if (!$routine) {
            return view('client.routine.index', [
                'routine' => null,
                'todayLogs' => collect(),
                'stats' => [
                    'completion_today' => 0,
                    'total_items' => 0,
                    'current_streak' => 0,
                ],
            ]);
        }

        // Get today's logs
        $todayLogs = RoutineLog::where('patient_id', $patient->id)
            ->whereDate('log_date', today())
            ->get()
            ->keyBy('routine_item_id');

        // Calculate stats
        $stats = [
            'completion_today' => $todayLogs->where('is_completed', true)->count(),
            'total_items' => $routine->items->count(),
            'current_streak' => $this->getCurrentStreak($patient->id),
        ];

        return view('client.routine.index', compact('routine', 'todayLogs', 'stats'));
    }

    /**
     * Log routine item completion.
     */
    public function logItem(Request $request, $itemId)
    {
        $user = $request->user();
        $patient = PatientProfile::where('user_id', $user->id)->firstOrFail();

        $item = RoutineItem::findOrFail($itemId);

        // Verify this item belongs to patient's routine
        $routine = Routine::where('id', $item->routine_id)
            ->where('patient_id', $patient->id)
            ->firstOrFail();

        $validated = $request->validate([
            'is_completed' => 'required|boolean',
            'is_skipped' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        $log = RoutineLog::updateOrCreate(
            [
                'routine_item_id' => $item->id,
                'patient_id' => $patient->id,
                'log_date' => today(),
            ],
            [
                'completed_at' => $validated['is_completed'] ? now()->format('H:i:s') : null,
                'is_completed' => $validated['is_completed'],
                'is_skipped' => $validated['is_skipped'] ?? false,
                'notes' => $validated['notes'] ?? null,
            ]
        );

        // Add progression if completed
        if ($validated['is_completed']) {
            $routine->addProgression(
                $patient->id,
                today()->format('Y-m-d'),
                100,
                'completed',
                'self',
                $user->id,
                'Routine item completed'
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Routine item logged successfully!',
        ]);
    }

    /**
     * Get current streak of consecutive days with all items completed.
     */
    private function getCurrentStreak($patientId)
    {
        $routine = Routine::where('patient_id', $patientId)
            ->where('is_active', true)
            ->first();

        if (!$routine) {
            return 0;
        }

        $totalItems = $routine->items->count();
        if ($totalItems === 0) {
            return 0;
        }

        $streak = 0;
        $currentDate = Carbon::today();
        
        while (true) {
            $completedCount = RoutineLog::whereHas('routineItem', function($query) use ($routine) {
                $query->where('routine_id', $routine->id);
            })
            ->where('patient_id', $patientId)
            ->whereDate('log_date', $currentDate)
            ->where('is_completed', true)
            ->count();
            
            if ($completedCount === $totalItems) {
                $streak++;
                $currentDate = $currentDate->subDay();
            } else {
                break;
            }
        }
        
        return $streak;
    }
}
