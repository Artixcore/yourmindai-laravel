<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Session;
use App\Models\SessionDay;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SessionDayController extends Controller
{
    /**
     * Store a newly created day entry or update if exists.
     */
    public function store(Request $request, Patient $patient, Session $session)
    {
        $this->authorize('view', $patient);
        $this->authorize('view', $session);
        $this->authorize('create', SessionDay::class);

        // Ensure session belongs to patient
        if ($session->patient_id !== $patient->id) {
            abort(404);
        }

        $validated = $request->validate([
            'day_date' => 'required|date',
            'symptoms' => 'nullable|string',
            'alerts' => 'nullable|string',
            'tasks' => 'nullable|string',
        ]);

        // Check if day entry already exists for this date
        $existingDay = SessionDay::where('session_id', $session->id)
            ->where('day_date', $validated['day_date'])
            ->first();

        if ($existingDay) {
            // Update existing entry
            $this->authorize('update', $existingDay);
            $existingDay->update([
                'symptoms' => $validated['symptoms'] ?? null,
                'alerts' => $validated['alerts'] ?? null,
                'tasks' => $validated['tasks'] ?? null,
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Day entry updated successfully!',
                    'data' => $existingDay,
                ]);
            }

            return redirect()
                ->route('patients.sessions.show', [$patient, $session])
                ->with('success', 'Day entry updated successfully!');
        }

        // Create new entry
        $day = SessionDay::create([
            'session_id' => $session->id,
            'day_date' => $validated['day_date'],
            'symptoms' => $validated['symptoms'] ?? null,
            'alerts' => $validated['alerts'] ?? null,
            'tasks' => $validated['tasks'] ?? null,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Day entry created successfully!',
                'data' => $day,
            ], 201);
        }

        return redirect()
            ->route('patients.sessions.show', [$patient, $session])
            ->with('success', 'Day entry created successfully!');
    }

    /**
     * Update the specified day entry.
     */
    public function update(Request $request, Patient $patient, Session $session, SessionDay $day)
    {
        $this->authorize('view', $patient);
        $this->authorize('view', $session);
        $this->authorize('update', $day);

        // Ensure session belongs to patient and day belongs to session
        if ($session->patient_id !== $patient->id || $day->session_id !== $session->id) {
            abort(404);
        }

        $validated = $request->validate([
            'day_date' => 'required|date',
            'symptoms' => 'nullable|string',
            'alerts' => 'nullable|string',
            'tasks' => 'nullable|string',
        ]);

        // If date changed, check for unique constraint
        if ($validated['day_date'] !== $day->day_date->format('Y-m-d')) {
            $existingDay = SessionDay::where('session_id', $session->id)
                ->where('day_date', $validated['day_date'])
                ->where('id', '!=', $day->id)
                ->first();

            if ($existingDay) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'error' => 'A day entry already exists for this date.',
                    ], 422);
                }

                return redirect()
                    ->back()
                    ->withErrors(['day_date' => 'A day entry already exists for this date.'])
                    ->withInput();
            }
        }

        $day->update($validated);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Day entry updated successfully!',
                'data' => $day->fresh(),
            ]);
        }

        return redirect()
            ->route('patients.sessions.show', [$patient, $session])
            ->with('success', 'Day entry updated successfully!');
    }

    /**
     * Remove the specified day entry.
     */
    public function destroy(Patient $patient, Session $session, SessionDay $day)
    {
        $this->authorize('view', $patient);
        $this->authorize('view', $session);
        $this->authorize('delete', $day);

        // Ensure session belongs to patient and day belongs to session
        if ($session->patient_id !== $patient->id || $day->session_id !== $session->id) {
            abort(404);
        }

        $day->delete();

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Day entry deleted successfully!',
            ]);
        }

        return redirect()
            ->route('patients.sessions.show', [$patient, $session])
            ->with('success', 'Day entry deleted successfully!');
    }
}
