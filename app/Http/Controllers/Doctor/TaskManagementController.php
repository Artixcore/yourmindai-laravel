<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\PatientProfile;
use App\Models\User;
use App\Notifications\TaskAssignedNotification;

class TaskManagementController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        
        $query = Task::where('assigned_by_doctor_id', $user->id)
            ->with(['patient.user']);

        // Apply filters
        if ($request->filled('patient_id')) {
            $query->where('patient_id', $request->patient_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->where('due_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('due_date', '<=', $request->date_to);
        }

        $tasks = $query->orderBy('due_date', 'desc')->paginate(15);

        // Get doctor's patients for filter dropdown
        $patients = PatientProfile::where('doctor_id', $user->id)->with('user')->get();

        // Calculate statistics
        $stats = [
            'total' => Task::where('assigned_by_doctor_id', $user->id)->count(),
            'pending' => Task::where('assigned_by_doctor_id', $user->id)->where('status', '!=', 'completed')->count(),
            'completed' => Task::where('assigned_by_doctor_id', $user->id)->where('status', 'completed')->count(),
            'overdue' => Task::where('assigned_by_doctor_id', $user->id)
                ->where('due_date', '<', now())
                ->where('status', '!=', 'completed')
                ->count(),
        ];

        return view('doctor.tasks.index', compact('tasks', 'patients', 'stats'));
    }

    public function create(Request $request)
    {
        $user = $request->user();
        $patientId = $request->input('patient_id');
        
        // Get doctor's patients
        $patients = PatientProfile::where('doctor_id', $user->id)->with('user')->get();
        
        $patient = null;
        if ($patientId) {
            $patient = PatientProfile::where('id', $patientId)
                ->where('doctor_id', $user->id)
                ->with('user')
                ->firstOrFail();
        }

        return view('doctor.tasks.create', compact('patients', 'patient'));
    }

    public function store(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'patient_id' => 'required|exists:patient_profiles,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'required|date',
            'points' => 'nullable|integer|min:0',
            'visible_to_patient' => 'boolean',
            'visible_to_parent' => 'boolean',
        ]);

        // Verify doctor has access to this patient
        $patient = PatientProfile::where('id', $validated['patient_id'])
            ->where('doctor_id', $user->id)
            ->firstOrFail();

        $validated['assigned_by_doctor_id'] = $user->id;
        $validated['status'] = 'pending';
        $validated['visible_to_patient'] = $request->has('visible_to_patient');
        $validated['visible_to_parent'] = $request->has('visible_to_parent');
        $validated['points'] = $validated['points'] ?? 0;

        $task = Task::create($validated);

        $clientUser = $patient->user;
        if ($clientUser && ($validated['visible_to_patient'] ?? false)) {
            $clientUser->notify(new TaskAssignedNotification(
                $task,
                'New Task Assigned',
                "A new task \"{$task->title}\" has been assigned to you.",
                route('client.tasks.show', $task)
            ));
        }

        return redirect()->route('doctor.tasks.show', $task->id)
            ->with('success', 'Task created successfully.');
    }

    public function show($id)
    {
        $user = auth()->user();
        
        $task = Task::with(['patient.user'])->findOrFail($id);

        // Verify doctor owns this task
        if ($task->assigned_by_doctor_id != $user->id) {
            abort(403, 'Unauthorized access to task');
        }

        return view('doctor.tasks.show', compact('task'));
    }

    public function edit($id)
    {
        $user = auth()->user();
        
        $task = Task::findOrFail($id);

        // Verify doctor owns this task
        if ($task->assigned_by_doctor_id != $user->id) {
            abort(403, 'Unauthorized access to task');
        }

        // Cannot edit completed tasks
        if ($task->status == 'completed') {
            return redirect()->route('doctor.tasks.show', $task->id)
                ->with('error', 'Cannot edit completed task.');
        }

        $patient = $task->patient;
        $patients = PatientProfile::where('doctor_id', $user->id)->with('user')->get();

        return view('doctor.tasks.edit', compact('task', 'patients', 'patient'));
    }

    public function update(Request $request, $id)
    {
        $user = auth()->user();
        
        $task = Task::findOrFail($id);

        // Verify doctor owns this task
        if ($task->assigned_by_doctor_id != $user->id) {
            abort(403, 'Unauthorized access to task');
        }

        // Cannot edit completed tasks
        if ($task->status == 'completed') {
            return redirect()->route('doctor.tasks.show', $task->id)
                ->with('error', 'Cannot edit completed task.');
        }

        $validated = $request->validate([
            'patient_id' => 'required|exists:patient_profiles,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'required|date',
            'points' => 'nullable|integer|min:0',
            'visible_to_patient' => 'boolean',
            'visible_to_parent' => 'boolean',
            'status' => 'nullable|in:pending,in_progress,completed,cancelled',
        ]);

        $validated['visible_to_patient'] = $request->has('visible_to_patient');
        $validated['visible_to_parent'] = $request->has('visible_to_parent');
        $validated['points'] = $validated['points'] ?? 0;

        $task->update($validated);

        return redirect()->route('doctor.tasks.show', $task->id)
            ->with('success', 'Task updated successfully.');
    }

    public function destroy($id)
    {
        $user = auth()->user();
        
        $task = Task::findOrFail($id);

        // Verify doctor owns this task
        if ($task->assigned_by_doctor_id != $user->id) {
            abort(403, 'Unauthorized access to task');
        }

        $task->delete();

        return redirect()->route('doctor.tasks.index')
            ->with('success', 'Task deleted successfully.');
    }

    public function patientTasks(Request $request, PatientProfile $patient)
    {
        $user = $request->user();
        
        if (!$this->canAccessPatient($user, $patient)) {
            abort(403, 'Unauthorized access to patient data');
        }

        $tasks = Task::where('patient_id', $patient->id)
            ->with('assignedByDoctor')
            ->orderBy('due_date', 'desc')
            ->get();

        // Calculate patient statistics
        $stats = [
            'total' => $tasks->count(),
            'pending' => $tasks->where('status', '!=', 'completed')->count(),
            'completed' => $tasks->where('status', 'completed')->count(),
            'overdue' => $tasks->where('due_date', '<', now())
                ->where('status', '!=', 'completed')->count(),
            'total_points_earned' => $tasks->where('status', 'completed')->sum('points'),
        ];

        return view('doctor.patients.tasks.index', compact('patient', 'tasks', 'stats'));
    }

    protected function canAccessPatient(User $user, PatientProfile $patient): bool
    {
        if ($user->role === 'admin') {
            return true;
        }
        
        if ($user->role === 'doctor') {
            return $patient->doctor_id === $user->id || 
                   $user->assignedDoctors()->where('doctor_id', $patient->doctor_id)->exists();
        }
        
        return false;
    }
}
