<?php

namespace App\Http\Controllers;

use App\Models\PatientProfile;
use App\Models\Task;
use App\Models\TaskVerification;
use App\Models\SupervisorLink;
use Illuminate\Http\Request;

class SupervisionTaskController extends Controller
{
    public function index(Request $request)
    {
        $supervisor = $request->user();

        $children = PatientProfile::whereHas('supervisorLinks', function ($query) use ($supervisor) {
            $query->where('supervisor_id', $supervisor->id);
        })->with('user')->get();

        if ($children->isEmpty()) {
            return redirect()->route('supervision.dashboard')
                ->with('info', 'No clients linked to your account. Contact your healthcare provider.');
        }

        if ($children->count() === 1) {
            return redirect()->route('supervision.child.tasks', $children->first());
        }

        $children->load('doctor');
        return view('supervision.tasks.select-child', compact('children'));
    }

    public function childTasks(Request $request, $patientId)
    {
        $supervisor = $request->user();

        $patient = PatientProfile::whereHas('supervisorLinks', function ($query) use ($supervisor) {
            $query->where('supervisor_id', $supervisor->id);
        })->with('user')->findOrFail($patientId);

        $tasks = Task::where('patient_id', $patient->id)
            ->with([
                'assignedByDoctor',
                'verifications' => fn ($q) => $q->where('parent_user_id', $supervisor->id)->where('verifier_role', 'supervision'),
            ])
            ->orderByRaw("CASE WHEN status = 'completed' THEN 1 ELSE 0 END")
            ->orderBy('due_date', 'asc')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('supervision.tasks.index', compact('patient', 'tasks'));
    }

    public function verify(Request $request, Task $task)
    {
        $supervisor = $request->user();

        if (!SupervisorLink::where('supervisor_id', $supervisor->id)->where('patient_id', $task->patient_id)->exists()) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'You can only verify tasks for linked clients.'], 403);
            }
            return back()->with('error', 'You can only verify tasks for linked clients.');
        }

        $verified = $request->boolean('verified', true);
        $remarks = $request->input('remarks');

        $existing = TaskVerification::where('task_id', $task->id)
            ->where('parent_user_id', $supervisor->id)
            ->where('verifier_role', 'supervision')
            ->first();

        if ($verified) {
            if ($existing) {
                $existing->update([
                    'verified_at' => now(),
                    'remarks' => $remarks,
                ]);
            } else {
                TaskVerification::create([
                    'task_id' => $task->id,
                    'parent_user_id' => $supervisor->id,
                    'verifier_role' => 'supervision',
                    'verified_at' => now(),
                    'remarks' => $remarks,
                ]);
            }
            $message = 'Task marked as verified.';
        } else {
            if ($existing) {
                $existing->delete();
            }
            $message = 'Verification removed.';
        }

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'verified' => $verified,
            ]);
        }

        $patient = $task->patient;
        return redirect()->route('supervision.child.tasks', $patient)
            ->with('success', $message);
    }
}
