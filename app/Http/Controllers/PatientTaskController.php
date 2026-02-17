<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\PatientProfile;
use App\Models\PatientPoints;
use App\Models\Patient;
use App\Notifications\TaskCompletedNotification;
use Illuminate\Http\Request;

class PatientTaskController extends Controller
{
    /**
     * Display a listing of tasks for the authenticated patient.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        
        // Get patient profile
        $patientProfile = PatientProfile::where('user_id', $user->id)->first();
        
        // If no patient profile, try to get patient by email
        if (!$patientProfile) {
            $patient = Patient::where('email', $user->email)->first();
            if ($patient) {
                // Try to find patient profile by matching name/phone
                $patientProfile = PatientProfile::where('doctor_id', $patient->doctor_id)
                    ->where(function($query) use ($patient) {
                        $query->where('full_name', $patient->name)
                              ->orWhere('phone', $patient->phone);
                    })
                    ->first();
            }
        }
        
        if (!$patientProfile) {
            return redirect()->route('patient.dashboard')
                ->with('error', 'Patient profile not found. Please contact your healthcare provider.');
        }
        
        // Get tasks
        $query = Task::where('patient_id', $patientProfile->id)
            ->where('visible_to_patient', true)
            ->with('assignedByDoctor');
        
        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'pending') {
                $query->where('status', '!=', 'completed');
            } else {
                $query->where('status', $request->status);
            }
        } else {
            // Default: show pending tasks first
            $query->orderByRaw("CASE WHEN status = 'completed' THEN 1 ELSE 0 END")
                  ->orderBy('due_date', 'asc')
                  ->orderBy('created_at', 'desc');
        }
        
        $tasks = $query->get();
        
        // Get patient points
        $points = PatientPoints::where('user_id', $user->id)->first();
        $totalPoints = $points ? $points->total_points : 0;
        
        // Calculate stats
        $stats = [
            'total' => $tasks->count(),
            'pending' => $tasks->where('status', '!=', 'completed')->count(),
            'completed' => $tasks->where('status', 'completed')->count(),
        ];
        
        return view('patient.tasks.index', compact('tasks', 'stats', 'totalPoints'));
    }
    
    /**
     * Display the specified task.
     */
    public function show($id)
    {
        $user = auth()->user();
        
        // Get patient profile
        $patientProfile = PatientProfile::where('user_id', $user->id)->first();
        
        if (!$patientProfile) {
            $patient = Patient::where('email', $user->email)->first();
            if ($patient) {
                $patientProfile = PatientProfile::where('doctor_id', $patient->doctor_id)
                    ->where(function($query) use ($patient) {
                        $query->where('full_name', $patient->name)
                              ->orWhere('phone', $patient->phone);
                    })
                    ->first();
            }
        }
        
        if (!$patientProfile) {
            return redirect()->route('patient.tasks.index')
                ->with('error', 'Patient profile not found.');
        }
        
        $task = Task::where('id', $id)
            ->where('patient_id', $patientProfile->id)
            ->where('visible_to_patient', true)
            ->with('assignedByDoctor')
            ->firstOrFail();
        
        return view('patient.tasks.show', compact('task'));
    }
    
    /**
     * Mark a task as complete.
     */
    public function complete($id)
    {
        $user = auth()->user();
        
        // Get patient profile
        $patientProfile = PatientProfile::where('user_id', $user->id)->first();
        
        if (!$patientProfile) {
            $patient = Patient::where('email', $user->email)->first();
            if ($patient) {
                $patientProfile = PatientProfile::where('doctor_id', $patient->doctor_id)
                    ->where(function($query) use ($patient) {
                        $query->where('full_name', $patient->name)
                              ->orWhere('phone', $patient->phone);
                    })
                    ->first();
            }
        }
        
        if (!$patientProfile) {
            return redirect()->route('patient.tasks.index')
                ->with('error', 'Patient profile not found.');
        }
        
        $task = Task::where('id', $id)
            ->where('patient_id', $patientProfile->id)
            ->where('visible_to_patient', true)
            ->firstOrFail();
        
        if ($task->status === 'completed') {
            return redirect()->route('patient.tasks.show', $task)
                ->with('info', 'This task is already completed.');
        }
        
        // Update task
        $task->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        $doctor = $task->assignedByDoctor;
        if ($doctor) {
            $doctor->notify(new TaskCompletedNotification(
                $task,
                'Task Completed',
                "A task \"{$task->title}\" has been completed by your patient.",
                route('tasks.show', $task)
            ));
        }
        
        // Award points if task has points
        if ($task->points > 0) {
            $patientPoints = PatientPoints::firstOrCreate(
                ['user_id' => $user->id],
                ['total_points' => 0]
            );
            
            $patientPoints->increment('total_points', $task->points);
            $patientPoints->save();
        }
        
        return redirect()->route('patient.tasks.show', $task)
            ->with('success', 'Task completed successfully!' . ($task->points > 0 ? " You earned {$task->points} points!" : ''));
    }
}
