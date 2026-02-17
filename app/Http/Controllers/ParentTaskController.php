<?php

namespace App\Http\Controllers;

use App\Http\Requests\VerifyTaskRequest;
use App\Models\PatientProfile;
use App\Models\Task;
use App\Models\TaskVerification;
use Illuminate\Http\Request;

class ParentTaskController extends Controller
{
    /**
     * Redirect to first child's tasks or show child selector.
     */
    public function index(Request $request)
    {
        $parent = $request->user();

        $children = PatientProfile::whereHas('parentLinks', function ($query) use ($parent) {
            $query->where('parent_id', $parent->id);
        })->with('user')->get();

        if ($children->isEmpty()) {
            return redirect()->route('parent.dashboard')
                ->with('info', 'No children linked to your account. Contact your healthcare provider.');
        }

        if ($children->count() === 1) {
            return redirect()->route('parent.child.tasks', $children->first());
        }

        $children->load('doctor');
        return view('parent.tasks.select-child', compact('children'));
    }

    /**
     * List tasks for a specific child.
     */
    public function childTasks(Request $request, $patientId)
    {
        $parent = $request->user();

        $patient = PatientProfile::whereHas('parentLinks', function ($query) use ($parent) {
            $query->where('parent_id', $parent->id);
        })->with('user')->findOrFail($patientId);

        $tasks = Task::where('patient_id', $patient->id)
            ->where('visible_to_parent', true)
            ->with(['assignedByDoctor', 'verifications' => fn ($q) => $q->where('parent_user_id', $parent->id)])
            ->orderByRaw("CASE WHEN status = 'completed' THEN 1 ELSE 0 END")
            ->orderBy('due_date', 'asc')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('parent.tasks.index', compact('patient', 'tasks'));
    }

    /**
     * Toggle verification for a task (AJAX or form POST).
     */
    public function verify(VerifyTaskRequest $request, Task $task)
    {
        $parent = $request->user();
        $verified = $request->boolean('verified', true);

        $existing = TaskVerification::where('task_id', $task->id)
            ->where('parent_user_id', $parent->id)
            ->first();

        if ($verified) {
            if (!$existing) {
                TaskVerification::create([
                    'task_id' => $task->id,
                    'parent_user_id' => $parent->id,
                    'verifier_role' => 'parent',
                    'verified_at' => now(),
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
        return redirect()->route('parent.child.tasks', $patient)
            ->with('success', $message);
    }
}
