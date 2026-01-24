<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Routine;
use App\Models\RoutineItem;
use App\Models\PatientProfile;
use App\Models\User;

class RoutineController extends Controller
{
    /**
     * Show all routines for a patient.
     */
    public function index(Request $request, $patientId)
    {
        $patient = PatientProfile::with('user')->findOrFail($patientId);
        
        // Check authorization
        if (!$this->canAccessPatient($request->user(), $patient)) {
            abort(403);
        }

        $routines = Routine::where('patient_id', $patientId)
            ->with(['items', 'createdByDoctor'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('doctor.patients.routines.index', compact('patient', 'routines'));
    }

    /**
     * Show form to create routine for patient.
     */
    public function create(Request $request, $patientId)
    {
        $patient = PatientProfile::with('user')->findOrFail($patientId);
        
        // Check authorization
        if (!$this->canAccessPatient($request->user(), $patient)) {
            abort(403);
        }

        return view('doctor.patients.routines.create', compact('patient'));
    }

    /**
     * Store routine for patient.
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
            'frequency' => 'required|in:daily,weekdays,weekends,custom',
            'start_time' => 'nullable|date_format:H:i',
            'is_active' => 'boolean',
            'items' => 'required|array|min:1',
            'items.*.title' => 'required|string',
            'items.*.description' => 'nullable|string',
            'items.*.time_of_day' => 'required|in:morning,afternoon,evening,night,anytime',
            'items.*.scheduled_time' => 'nullable|date_format:H:i',
            'items.*.estimated_minutes' => 'nullable|integer',
            'items.*.is_required' => 'boolean',
        ]);

        // Create routine
        $routine = Routine::create([
            'patient_id' => $patient->id,
            'created_by' => $request->user()->id,
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'frequency' => $validated['frequency'],
            'start_time' => $validated['start_time'] ?? null,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        // Create routine items
        foreach ($validated['items'] as $index => $itemData) {
            RoutineItem::create([
                'routine_id' => $routine->id,
                'order' => $index,
                'title' => $itemData['title'],
                'description' => $itemData['description'] ?? null,
                'time_of_day' => $itemData['time_of_day'],
                'scheduled_time' => $itemData['scheduled_time'] ?? null,
                'estimated_minutes' => $itemData['estimated_minutes'] ?? null,
                'is_required' => $itemData['is_required'] ?? true,
            ]);
        }

        return redirect()->route('patients.routines.index', $patient->id)
            ->with('success', 'Routine created successfully!');
    }

    /**
     * Show specific routine details.
     */
    public function show(Request $request, $patientId, $routineId)
    {
        $patient = PatientProfile::with('user')->findOrFail($patientId);
        $routine = Routine::where('id', $routineId)
            ->where('patient_id', $patientId)
            ->with(['items' => function($query) {
                $query->orderBy('order');
            }])
            ->firstOrFail();

        // Check authorization
        if (!$this->canAccessPatient($request->user(), $patient)) {
            abort(403);
        }

        return view('doctor.patients.routines.show', compact('patient', 'routine'));
    }

    /**
     * Toggle routine active status.
     */
    public function toggleActive(Request $request, $patientId, $routineId)
    {
        $patient = PatientProfile::findOrFail($patientId);
        $routine = Routine::where('id', $routineId)
            ->where('patient_id', $patientId)
            ->firstOrFail();

        // Check authorization
        if (!$this->canAccessPatient($request->user(), $patient)) {
            abort(403);
        }

        $routine->update(['is_active' => !$routine->is_active]);

        return redirect()->route('patients.routines.index', $patient->id)
            ->with('success', 'Routine status updated!');
    }

    /**
     * Check if doctor can access this patient.
     */
    private function canAccessPatient($user, $patient)
    {
        return $user->isAdmin() || $user->id === $patient->doctor_id;
    }
}
