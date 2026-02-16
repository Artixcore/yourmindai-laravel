<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Routine;
use App\Models\RoutineItem;
use App\Models\Patient;
use App\Models\PatientProfile;
use App\Models\User;

class RoutineController extends Controller
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
     * Show all routines for a patient.
     */
    public function index(Request $request, Patient $patient)
    {
        $patientProfile = $this->resolvePatientProfile($patient);
        
        // Check authorization
        if (!$this->canAccessPatient($request->user(), $patientProfile)) {
            abort(403);
        }

        $routines = Routine::where('patient_id', $patientProfile->id)
            ->with(['items', 'createdByDoctor'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('doctor.patients.routines.index', compact('patient', 'patientProfile', 'routines'));
    }

    /**
     * Show form to create routine for patient.
     */
    public function create(Request $request, Patient $patient)
    {
        $patientProfile = $this->resolvePatientProfile($patient);
        
        // Check authorization
        if (!$this->canAccessPatient($request->user(), $patientProfile)) {
            abort(403);
        }

        return view('doctor.patients.routines.create', compact('patient', 'patientProfile'));
    }

    /**
     * Store routine for patient.
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
            'patient_id' => $patientProfile->id,
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

        return redirect()->route('patients.routines.index', $patient)
            ->with('success', 'Routine created successfully!');
    }

    /**
     * Show specific routine details.
     */
    public function show(Request $request, Patient $patient, $routineId)
    {
        $patientProfile = $this->resolvePatientProfile($patient);
        $routine = Routine::where('id', $routineId)
            ->where('patient_id', $patientProfile->id)
            ->with(['items' => function($query) {
                $query->orderBy('order');
            }])
            ->firstOrFail();

        // Check authorization
        if (!$this->canAccessPatient($request->user(), $patientProfile)) {
            abort(403);
        }

        return view('doctor.patients.routines.show', compact('patient', 'patientProfile', 'routine'));
    }

    /**
     * Toggle routine active status.
     */
    public function toggleActive(Request $request, Patient $patient, $routineId)
    {
        $patientProfile = $this->resolvePatientProfile($patient);
        $routine = Routine::where('id', $routineId)
            ->where('patient_id', $patientProfile->id)
            ->firstOrFail();

        // Check authorization
        if (!$this->canAccessPatient($request->user(), $patientProfile)) {
            abort(403);
        }

        $routine->update(['is_active' => !$routine->is_active]);

        return redirect()->route('patients.routines.index', $patient)
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
