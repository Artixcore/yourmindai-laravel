<?php

namespace App\Http\Controllers;

use App\Models\PatientProfile;
use App\Models\ClinicalNote;
use App\Models\Goal;
use App\Models\Task;
use App\Models\Reminder;
use App\Models\DoctorInstruction;
use App\Models\PatientPoints;
use App\Models\ParentLink;
use App\Services\OpenAIService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class PatientController extends Controller
{
    protected $openAIService;

    public function __construct(OpenAIService $openAIService)
    {
        $this->openAIService = $openAIService;
    }

    /**
     * Check if user can access a patient
     */
    private function canAccessPatient(string $userId, string $userRole, string $patientProfileId): bool
    {
        $profile = PatientProfile::find($patientProfileId);

        if (!$profile) {
            return false;
        }

        // Doctor can access their own patients
        if (in_array($userRole, ['DOCTOR', 'THERAPIST']) && (string) $profile->doctor_id === $userId) {
            return true;
        }

        // Patient can access their own profile
        if ($userRole === 'PATIENT' && (string) $profile->user_id === $userId) {
            return true;
        }

        // TODO: Implement parent-to-patient relationship mapping
        if ($userRole === 'PARENT') {
            $link = ParentLink::where('parent_id', $userId)
                ->where('patient_id', $patientProfileId)
                ->first();
            return $link !== null;
        }

        return false;
    }

    /**
     * GET /api/patients/{id}
     * Get patient profile by ID
     */
    public function show(Request $request, string $id)
    {
        $userId = (string) $request->user()->id;
        $role = $request->user()->role;

        $profile = PatientProfile::with(['user', 'doctor'])->find($id);

        if (!$profile) {
            return response()->json([
                'success' => false,
                'error' => "We couldn't find this patient.",
            ], 404);
        }

        if (!$this->canAccessPatient($userId, $role, $id)) {
            return response()->json([
                'success' => false,
                'error' => "You don't have permission to view this patient's information.",
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'profile' => [
                    'id' => (string) $profile->id,
                    'patientNumber' => $profile->patient_number,
                    'userId' => (string) $profile->user_id,
                    'doctorId' => (string) $profile->doctor_id,
                    'fullName' => $profile->full_name,
                    'dateOfBirth' => $profile->date_of_birth->format('Y-m-d'),
                    'gender' => $profile->gender,
                    'phone' => $profile->phone,
                    'problem' => $profile->problem,
                    'status' => $profile->status,
                    'createdAt' => $profile->created_at->toISOString(),
                    'updatedAt' => $profile->updated_at->toISOString(),
                    'user' => $profile->user ? [
                        'id' => (string) $profile->user->_id,
                        'email' => $profile->user->email,
                        'username' => $profile->user->username,
                        'role' => $profile->user->role,
                        'createdAt' => $profile->user->created_at->toISOString(),
                    ] : null,
                    'doctor' => $profile->doctor ? [
                        'id' => (string) $profile->doctor->_id,
                        'email' => $profile->doctor->email,
                    ] : null,
                ],
            ],
        ]);
    }

    /**
     * GET /api/patients/{id}/notes
     * Get clinical notes for a patient
     */
    public function getNotes(Request $request, string $id)
    {
        $userId = (string) $request->user()->id;
        $role = $request->user()->role;

        if (!in_array($role, ['DOCTOR', 'THERAPIST'])) {
            return response()->json([
                'success' => false,
                'error' => 'Only healthcare providers can view clinical notes.',
            ], 403);
        }

        if (!$this->canAccessPatient($userId, $role, $id)) {
            return response()->json([
                'success' => false,
                'error' => "You don't have permission to view this patient's information.",
            ], 403);
        }

        $notes = ClinicalNote::where('patient_id', $id)
            ->with('author')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'notes' => $notes->map(function ($note) {
                    return [
                        'id' => (string) $note->id,
                        'patientId' => (string) $note->patient_id,
                        'authorId' => (string) $note->author_id,
                        'rawText' => $note->raw_text,
                        'aiSummary' => $note->ai_summary,
                        'createdAt' => $note->created_at->toISOString(),
                        'author' => $note->author ? [
                            'id' => (string) $note->author->id,
                            'email' => $note->author->email,
                            'role' => $note->author->role,
                        ] : null,
                    ];
                }),
            ],
        ]);
    }

    /**
     * POST /api/patients/{id}/notes
     * Create a clinical note with AI summarization
     */
    public function createNote(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'rawText' => 'required|string|min:10',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => $validator->errors()->first(),
            ], 400);
        }

        $userId = (string) $request->user()->id;
        $role = $request->user()->role;

        if (!in_array($role, ['DOCTOR', 'THERAPIST'])) {
            return response()->json([
                'success' => false,
                'error' => 'Only healthcare providers can create clinical notes.',
            ], 403);
        }

        if (!$this->canAccessPatient($userId, $role, $id)) {
            return response()->json([
                'success' => false,
                'error' => "You don't have permission to view this patient's information.",
            ], 403);
        }

        // Generate AI summary (non-blocking)
        $aiSummary = null;
        try {
            $aiSummary = $this->openAIService->summarizeClinicalNote($request->rawText);
        } catch (\Exception $e) {
            // Continue without summary - don't fail the whole request
            \Log::error('AI summarization failed: ' . $e->getMessage());
        }

        $note = ClinicalNote::create([
            'patient_id' => $id,
            'author_id' => $userId,
            'raw_text' => $request->rawText,
            'ai_summary' => $aiSummary,
        ]);

        $note->load('author');

        return response()->json([
            'success' => true,
            'data' => [
                'note' => [
                    'id' => (string) $note->id,
                    'patientId' => (string) $note->patient_id,
                    'authorId' => (string) $note->author_id,
                    'rawText' => $note->raw_text,
                    'aiSummary' => $note->ai_summary,
                    'createdAt' => $note->created_at->toISOString(),
                    'author' => $note->author ? [
                        'id' => (string) $note->author->_id,
                        'email' => $note->author->email,
                        'role' => $note->author->role,
                    ] : null,
                ],
            ],
        ], 201);
    }

    /**
     * GET /api/patients/{id}/goals
     * Get goals for a patient
     */
    public function getGoals(Request $request, string $id)
    {
        $userId = (string) $request->user()->id;
        $role = $request->user()->role;

        if (!$this->canAccessPatient($userId, $role, $id)) {
            return response()->json([
                'success' => false,
                'error' => "You don't have permission to view this patient's information.",
            ], 403);
        }

        $query = Goal::where('patient_id', $id);

        // Filter visibility for patients/parents
        if ($role === 'PATIENT') {
            $query->where('visible_to_patient', true);
        } elseif ($role === 'PARENT') {
            $query->where('visible_to_parent', true);
        }

        $goals = $query->orderBy('created_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => [
                'goals' => $goals->map(function ($g) {
                    return [
                        'id' => (string) $g->id,
                        'patientId' => (string) $g->patient_id,
                        'title' => $g->title,
                        'description' => $g->description,
                        'status' => $g->status,
                        'visibleToPatient' => $g->visible_to_patient,
                        'visibleToParent' => $g->visible_to_parent,
                        'createdAt' => $g->created_at->toISOString(),
                        'updatedAt' => $g->updated_at->toISOString(),
                    ];
                }),
            ],
        ]);
    }

    /**
     * POST /api/patients/{id}/goals
     * Create a goal
     */
    public function createGoal(Request $request, string $id)
    {
        // TODO: Implement goal creation
        return response()->json([
            'success' => false,
            'error' => 'Not implemented yet',
        ], 501);
    }

    /**
     * GET /api/patients/{id}/tasks
     * Get tasks for a patient
     */
    public function getTasks(Request $request, string $id)
    {
        $userId = (string) $request->user()->id;
        $role = $request->user()->role;

        if (!$this->canAccessPatient($userId, $role, $id)) {
            return response()->json([
                'success' => false,
                'error' => "You don't have permission to view this patient's information.",
            ], 403);
        }

        $query = Task::where('patient_id', $id);

        // Filter visibility for patients/parents
        if ($role === 'PATIENT') {
            $query->where('visible_to_patient', true);
        } elseif ($role === 'PARENT') {
            $query->where('visible_to_parent', true);
        }

        $tasks = $query->orderBy('created_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => [
                'tasks' => $tasks->map(function ($t) {
                    return [
                        'id' => (string) $t->id,
                        'patientId' => (string) $t->patient_id,
                        'title' => $t->title,
                        'description' => $t->description,
                        'status' => $t->status,
                        'dueDate' => $t->due_date ? $t->due_date->toISOString() : null,
                        'points' => $t->points,
                        'assignedByDoctorId' => (string) $t->assigned_by_doctor_id,
                        'completedAt' => $t->completed_at ? $t->completed_at->toISOString() : null,
                        'createdAt' => $t->created_at->toISOString(),
                        'updatedAt' => $t->updated_at->toISOString(),
                    ];
                }),
            ],
        ]);
    }

    /**
     * GET /api/patients/me/dashboard
     * Get patient's own dashboard data
     */
    public function dashboard(Request $request)
    {
        $userId = (string) $request->user()->id;
        $role = $request->user()->role;

        if ($role !== 'PATIENT') {
            return response()->json([
                'success' => false,
                'error' => 'Only patients can access their dashboard.',
            ], 403);
        }

        $patientProfile = PatientProfile::where('user_id', $userId)
            ->with('doctor')
            ->first();

        if (!$patientProfile) {
            return response()->json([
                'success' => false,
                'error' => 'Patient profile not found.',
            ], 404);
        }

        $tasks = Task::where('patient_id', $patientProfile->id)
            ->where('visible_to_patient', true)
            ->with('assignedByDoctor')
            ->orderBy('created_at', 'desc')
            ->get();

        $patientPoints = PatientPoints::where('user_id', $userId)->first();

        $reminders = Reminder::where('user_id', $userId)
            ->where('is_active', true)
            ->orderBy('scheduled_time', 'asc')
            ->get();

        $instructions = DoctorInstruction::where('patient_id', $patientProfile->_id)
            ->with('doctor')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'profile' => [
                    'id' => (string) $patientProfile->id,
                    'fullName' => $patientProfile->full_name,
                    'patientNumber' => $patientProfile->patient_number,
                    'doctor' => $patientProfile->doctor ? [
                        'id' => (string) $patientProfile->doctor->id,
                        'email' => $patientProfile->doctor->email,
                    ] : null,
                ],
                'tasks' => $tasks->map(function ($t) {
                    return [
                        'id' => (string) $t->id,
                        'patientId' => (string) $t->patient_id,
                        'title' => $t->title,
                        'description' => $t->description,
                        'status' => $t->status,
                        'dueDate' => $t->due_date ? $t->due_date->toISOString() : null,
                        'points' => $t->points,
                        'assignedByDoctorId' => (string) $t->assigned_by_doctor_id,
                        'completedAt' => $t->completed_at ? $t->completed_at->toISOString() : null,
                        'assignedByDoctor' => $t->assignedByDoctor ? [
                            'id' => (string) $t->assignedByDoctor->id,
                            'email' => $t->assignedByDoctor->email,
                        ] : null,
                        'createdAt' => $t->created_at->toISOString(),
                        'updatedAt' => $t->updated_at->toISOString(),
                    ];
                }),
                'totalPoints' => $patientPoints ? $patientPoints->total_points : 0,
                'reminders' => $reminders->map(function ($r) {
                    return [
                        'id' => (string) $r->id,
                        'type' => $r->type,
                        'title' => $r->title,
                        'message' => $r->message,
                        'scheduledTime' => $r->scheduled_time->toISOString(),
                        'isActive' => $r->is_active,
                        'createdAt' => $r->created_at->toISOString(),
                    ];
                }),
                'instructions' => $instructions->map(function ($i) {
                    return [
                        'id' => (string) $i->id,
                        'instructionType' => $i->instruction_type,
                        'title' => $i->title,
                        'content' => $i->content,
                        'taskId' => $i->task_id ? (string) $i->task_id : null,
                        'doctor' => $i->doctor ? [
                            'id' => (string) $i->doctor->id,
                            'email' => $i->doctor->email,
                        ] : null,
                        'createdAt' => $i->created_at->toISOString(),
                        'updatedAt' => $i->updated_at->toISOString(),
                    ];
                }),
            ],
        ]);
    }

    // TODO: Implement remaining endpoints:
    // - createGoal, updateGoal, deleteGoal
    // - createTask, updateTask, deleteTask, completeTask
    // - reminders CRUD
    // - getInstructions

}
