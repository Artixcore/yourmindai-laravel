<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\PatientAuthController;
use App\Http\Controllers\Api\PatientController as ApiPatientController;
use App\Http\Controllers\Api\PatientSessionController;
use App\Http\Controllers\Api\PatientResourceController;
use App\Http\Controllers\AIController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\AssessmentController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ParentController;
use App\Http\Controllers\WebhookController;
use App\Http\Controllers\ClientIntakeController;
use App\Http\Controllers\PatientMessageController;
use App\Http\Controllers\PatientMedicationController;
use App\Http\Controllers\PatientJournalController;
use App\Http\Controllers\PatientProgressController;

// Health check
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now()->toISOString(),
    ]);
});

// Auth routes (public, rate limited to reduce brute force)
Route::post('/auth/register-doctor', [AuthController::class, 'registerDoctor'])->middleware('throttle:10,1');
Route::post('/auth/login', [AuthController::class, 'login'])->middleware('throttle:5,1');
Route::post('/auth/register-patient', [AuthController::class, 'registerPatient'])->middleware('throttle:10,1');
Route::post('/auth/register-parent', [AuthController::class, 'registerParent'])->middleware('throttle:10,1');

// Auth routes (protected)
Route::middleware('jwt.auth')->group(function () {
    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);
});

// Doctor routes (require DOCTOR/THERAPIST role)
Route::middleware(['jwt.auth', 'role:DOCTOR,THERAPIST'])->group(function () {
    Route::get('/doctors/patients', [DoctorController::class, 'listPatients']);
    Route::post('/doctors/patients', [DoctorController::class, 'createPatient']);
    Route::put('/doctors/patients/{id}', [DoctorController::class, 'updatePatient']);
    Route::delete('/doctors/patients/{id}', [DoctorController::class, 'deletePatient']);
    Route::post('/doctors/patients/{id}/reset-password', [DoctorController::class, 'resetPassword']);
    // TODO: Doctor instructions endpoints
});

// Patient routes
Route::middleware('jwt.auth')->group(function () {
    Route::get('/patients/{id}', [PatientController::class, 'show']);
    Route::get('/patients/{id}/notes', [PatientController::class, 'getNotes']);
    Route::post('/patients/{id}/notes', [PatientController::class, 'createNote']);
    Route::get('/patients/{id}/goals', [PatientController::class, 'getGoals']);
    Route::post('/patients/{id}/goals', [PatientController::class, 'createGoal']);
    Route::put('/patients/{id}/goals/{goalId}', [PatientController::class, 'updateGoal']);
    Route::delete('/patients/{id}/goals/{goalId}', [PatientController::class, 'deleteGoal']);
    Route::get('/patients/{id}/tasks', [PatientController::class, 'getTasks']);
    Route::post('/patients/{id}/tasks', [PatientController::class, 'createTask']);
    Route::put('/patients/{id}/tasks/{taskId}', [PatientController::class, 'updateTask']);
    Route::put('/patients/{id}/tasks/{taskId}/complete', [PatientController::class, 'completeTask']);
    Route::delete('/patients/{id}/tasks/{taskId}', [PatientController::class, 'deleteTask']);
    Route::get('/patients/me/dashboard', [PatientController::class, 'dashboard']);
    Route::get('/patients/me/reminders', [PatientController::class, 'getReminders']);
    Route::post('/patients/me/reminders', [PatientController::class, 'createReminder']);
    Route::put('/patients/me/reminders/{id}', [PatientController::class, 'updateReminder']);
    Route::delete('/patients/me/reminders/{id}', [PatientController::class, 'deleteReminder']);
    Route::get('/patients/me/instructions', [PatientController::class, 'getInstructions']);
    
    // Patient Messages (JWT)
    Route::get('/patients/me/messages', [PatientMessageController::class, 'index']);
    Route::post('/patients/me/messages', [PatientMessageController::class, 'store']);
    Route::put('/patients/me/messages/{id}/read', [PatientMessageController::class, 'markAsRead']);
    
    // Patient Medications (JWT)
    Route::get('/patients/me/medications', [PatientMedicationController::class, 'index']);
    Route::post('/patients/me/medications/{id}/log', [PatientMedicationController::class, 'logIntake']);
    Route::get('/patients/me/medications/{id}/history', [PatientMedicationController::class, 'history']);
    
    // Patient Journal (JWT) – index and store only; update/destroy not implemented
    Route::get('/patients/me/journal', [PatientJournalController::class, 'index']);
    Route::post('/patients/me/journal', [PatientJournalController::class, 'store']);

    // Patient Progress (JWT)
    Route::get('/patients/me/progress', [PatientProgressController::class, 'index']);
    Route::get('/patients/me/stats', [PatientProgressController::class, 'index']);
});

// AI routes (DOCTOR/THERAPIST only)
Route::middleware(['jwt.auth', 'role:DOCTOR,THERAPIST'])->group(function () {
    Route::post('/ai/summarize-note', [AIController::class, 'summarizeNote']);
    Route::post('/ai/treatment-suggestions', [AIController::class, 'treatmentSuggestions']);
});

// Appointment routes
Route::middleware('jwt.auth')->group(function () {
    Route::get('/appointments/doctors', [AppointmentController::class, 'listDoctors']);
    Route::get('/appointments/doctors/{id}/availability', [AppointmentController::class, 'getAvailability']);
    Route::post('/appointments', [AppointmentController::class, 'create']);
    Route::get('/appointments/me', [AppointmentController::class, 'myAppointments']);
    Route::get('/appointments/{id}', [AppointmentController::class, 'show']);
    Route::patch('/appointments/{id}/reschedule', [AppointmentController::class, 'reschedule']);
    Route::patch('/appointments/{id}/cancel', [AppointmentController::class, 'cancel']);
});

// Assessment routes
Route::middleware('jwt.auth')->group(function () {
    Route::post('/assessments/assign', [AssessmentController::class, 'assign']);
    Route::get('/assessments/me', [AssessmentController::class, 'myAssessments']);
    Route::get('/assessments/patient/{patientId}', [AssessmentController::class, 'patientAssessments']);
    Route::post('/assessments/{id}/complete', [AssessmentController::class, 'complete']);
    Route::get('/assessments/{id}/result', [AssessmentController::class, 'getResult']);
    Route::get('/assessments/patient/{patientId}/history', [AssessmentController::class, 'getHistory']);
});

// Client intake routes (protected)
Route::middleware('jwt.auth')->group(function () {
    Route::post('/client/intake/persist', [ClientIntakeController::class, 'persist']);
    Route::get('/client/intake', [ClientIntakeController::class, 'index']);
});

// User management routes
Route::middleware('jwt.auth')->group(function () {
    Route::get('/users/me', [UserController::class, 'me']);
    Route::put('/users/me', [UserController::class, 'update']);
    Route::put('/users/me/password', [UserController::class, 'updatePassword']);
    Route::post('/users/invite-codes', [UserController::class, 'createInviteCode']);
    Route::get('/users/invite-codes', [UserController::class, 'listInviteCodes']);
    Route::delete('/users/invite-codes/{code}', [UserController::class, 'deleteInviteCode']);
});

// Parent routes
Route::middleware('jwt.auth')->group(function () {
    Route::get('/parents/children', [ParentController::class, 'listChildren']);
    Route::get('/parents/children/{id}', [ParentController::class, 'showChild']);
    Route::get('/parents/children/{id}/goals', [ParentController::class, 'getChildGoals']);
    Route::get('/parents/children/{id}/tasks', [ParentController::class, 'getChildTasks']);
});

// Patient API routes (Sanctum authentication)
Route::post('/patient/login', [PatientAuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/patient/logout', [PatientAuthController::class, 'logout']);
    Route::get('/patient/me', [ApiPatientController::class, 'me']);
    Route::get('/patient/sessions', [PatientSessionController::class, 'index']);
    Route::get('/patient/sessions/{session}', [PatientSessionController::class, 'show']);
    Route::get('/patient/sessions/{session}/days/{day}', [PatientSessionController::class, 'showDay']);
    Route::get('/patient/resources', [PatientResourceController::class, 'index']);
    
    // Patient Messages
    Route::get('/patient/messages', [PatientMessageController::class, 'index']);
    Route::post('/patient/messages', [PatientMessageController::class, 'store']);
    Route::put('/patient/messages/{id}/read', [PatientMessageController::class, 'markAsRead']);
    
    // Patient Medications
    Route::get('/patient/medications', [PatientMedicationController::class, 'index']);
    Route::post('/patient/medications/{id}/log', [PatientMedicationController::class, 'logIntake']);
    Route::get('/patient/medications/{id}/history', [PatientMedicationController::class, 'history']);
    
    // Patient Journal – index and store only
    Route::get('/patient/journal', [PatientJournalController::class, 'index']);
    Route::post('/patient/journal', [PatientJournalController::class, 'store']);

    // Patient Progress
    Route::get('/patient/progress', [PatientProgressController::class, 'index']);
});

// Webhook routes (no authentication required, but signature verification is done in controller)
Route::post('/webhook/deploy', [WebhookController::class, 'deploy']);
