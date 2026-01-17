<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Session;
use App\Models\SessionDay;
use Illuminate\Http\Request;

class PatientSessionController extends Controller
{
    /**
     * List all sessions for authenticated patient
     */
    public function index(Request $request)
    {
        $patient = $request->user('patient');

        $sessions = Session::where('patient_id', $patient->id)
            ->with(['doctor:id,name,email', 'days'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $sessions->map(function ($session) {
                return [
                    'id' => $session->id,
                    'title' => $session->title,
                    'notes' => $session->notes,
                    'status' => $session->status,
                    'doctor' => $session->doctor ? [
                        'id' => $session->doctor->id,
                        'name' => $session->doctor->name,
                        'email' => $session->doctor->email,
                    ] : null,
                    'days_count' => $session->days->count(),
                    'created_at' => $session->created_at->toISOString(),
                    'updated_at' => $session->updated_at->toISOString(),
                ];
            }),
        ]);
    }

    /**
     * Get single session with days
     */
    public function show(Request $request, $sessionId)
    {
        $patient = $request->user();

        $session = Session::where('patient_id', $patient->id)
            ->where('id', $sessionId)
            ->with(['doctor:id,name,email', 'days' => function ($query) {
                $query->orderBy('day_date', 'desc');
            }])
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $session->id,
                'title' => $session->title,
                'notes' => $session->notes,
                'status' => $session->status,
                'doctor' => $session->doctor ? [
                    'id' => $session->doctor->id,
                    'name' => $session->doctor->name,
                    'email' => $session->doctor->email,
                ] : null,
                'days' => $session->days->map(function ($day) {
                    return [
                        'id' => $day->id,
                        'day_date' => $day->day_date->format('Y-m-d'),
                        'symptoms' => $day->symptoms,
                        'alerts' => $day->alerts,
                        'tasks' => $day->tasks,
                        'created_at' => $day->created_at->toISOString(),
                        'updated_at' => $day->updated_at->toISOString(),
                    ];
                }),
                'created_at' => $session->created_at->toISOString(),
                'updated_at' => $session->updated_at->toISOString(),
            ],
        ]);
    }

    /**
     * Get specific session day
     */
    public function showDay(Request $request, $sessionId, $dayId)
    {
        $patient = $request->user();

        // First verify session belongs to patient
        $session = Session::where('patient_id', $patient->id)
            ->where('id', $sessionId)
            ->firstOrFail();

        // Then get the day and verify it belongs to the session
        $day = SessionDay::where('session_id', $session->id)
            ->where('id', $dayId)
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $day->id,
                'session_id' => $day->session_id,
                'day_date' => $day->day_date->format('Y-m-d'),
                'symptoms' => $day->symptoms,
                'alerts' => $day->alerts,
                'tasks' => $day->tasks,
                'created_at' => $day->created_at->toISOString(),
                'updated_at' => $day->updated_at->toISOString(),
            ],
        ]);
    }
}
