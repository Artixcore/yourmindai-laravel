<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Session;
use Illuminate\Http\Request;

class SessionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Patient $patient)
    {
        $this->authorize('view', $patient);
        $this->authorize('viewAny', Session::class);

        $user = auth()->user();
        
        // Scope sessions by doctor ownership
        $query = $patient->sessions()->with(['doctor', 'days']);
        
        if ($user->role !== 'admin') {
            $query->where('doctor_id', $user->id);
        }

        $sessions = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('sessions.index', [
            'patient' => $patient,
            'sessions' => $sessions,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Patient $patient)
    {
        $this->authorize('view', $patient);
        $this->authorize('create', Session::class);

        return view('sessions.create', [
            'patient' => $patient,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Patient $patient)
    {
        $this->authorize('view', $patient);
        $this->authorize('create', Session::class);

        $user = auth()->user();
        $rules = [
            'title' => 'required|string|max:255',
            'notes' => 'nullable|string',
            'status' => 'nullable|in:active,closed',
            'next_session_date' => 'nullable|date',
            'reminder_at' => 'nullable|date',
        ];
        if ($user->role === 'admin') {
            $rules['doctor_id'] = 'nullable|exists:users,id';
        }
        $validated = $request->validate($rules);

        $doctorId = $user->role === 'admin' && !empty($validated['doctor_id'])
            ? $validated['doctor_id']
            : $user->id;

        $session = Session::create([
            'doctor_id' => $doctorId,
            'patient_id' => $patient->id,
            'title' => $validated['title'],
            'notes' => $validated['notes'] ?? null,
            'status' => $validated['status'] ?? 'active',
            'next_session_date' => $validated['next_session_date'] ?? null,
            'reminder_at' => $validated['reminder_at'] ?? null,
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Session created successfully!',
                'redirect' => route('patients.sessions.show', [$patient, $session]),
            ]);
        }

        return redirect()
            ->route('patients.sessions.show', [$patient, $session])
            ->with('success', 'Session created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Patient $patient, Session $session)
    {
        $this->authorize('view', $patient);
        $this->authorize('view', $session);

        // Ensure session belongs to patient
        if ($session->patient_id !== $patient->id) {
            abort(404);
        }

        $session->load(['doctor', 'days' => function ($query) {
            $query->orderBy('day_date', 'desc');
        }]);

        return view('sessions.show', [
            'patient' => $patient,
            'session' => $session,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Patient $patient, Session $session)
    {
        $this->authorize('view', $patient);
        $this->authorize('update', $session);

        // Ensure session belongs to patient
        if ($session->patient_id !== $patient->id) {
            abort(404);
        }

        return view('sessions.edit', [
            'patient' => $patient,
            'session' => $session,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Patient $patient, Session $session)
    {
        $this->authorize('view', $patient);
        $this->authorize('update', $session);

        // Ensure session belongs to patient
        if ($session->patient_id !== $patient->id) {
            abort(404);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'session_type' => 'nullable|string|in:individual,group,skill_based,family,couple,parents,relapse_prevention,others',
            'notes' => 'nullable|string',
            'status' => 'required|in:active,closed',
            'next_session_date' => 'nullable|date',
            'reminder_at' => 'nullable|date',
        ]);

        $session->update($validated);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Session updated successfully!',
                'redirect' => route('patients.sessions.show', [$patient, $session]),
            ]);
        }

        return redirect()
            ->route('patients.sessions.show', [$patient, $session])
            ->with('success', 'Session updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Patient $patient, Session $session)
    {
        $this->authorize('view', $patient);
        $this->authorize('delete', $session);

        // Ensure session belongs to patient
        if ($session->patient_id !== $patient->id) {
            abort(404);
        }

        $session->delete();

        return redirect()
            ->route('patients.show', $patient)
            ->with('success', 'Session deleted successfully!');
    }
}
