<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DoctorProfileController;
use App\Http\Controllers\DoctorPaperController;
use App\Http\Controllers\WebPatientController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\SessionDayController;
use App\Http\Controllers\PatientResourceController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\Admin\AssistantAssignmentController;
use App\Http\Controllers\Admin\AdminPatientController;
use App\Http\Controllers\Admin\AdminSessionController;
use App\Http\Controllers\Admin\AnalyticsController;
use App\Http\Controllers\Admin\AiReportController;
use App\Http\Controllers\Admin\ContactController;
use App\Http\Controllers\PatientDashboardController;
use App\Http\Controllers\PatientSessionController;
use App\Http\Controllers\PatientAppointmentController;
use App\Http\Controllers\PatientAssessmentController;
use App\Http\Controllers\PatientProgressController;
use App\Http\Controllers\PatientMessageController;
use App\Http\Controllers\PatientMedicationController;
use App\Http\Controllers\PatientJournalController;
use App\Http\Controllers\DoctorAppointmentController;
use App\Http\Controllers\DoctorMessageController;
use App\Http\Controllers\PsychometricScaleController;
use App\Http\Controllers\PsychometricAssessmentController;
use App\Http\Controllers\ContingencyPlanController;

// Landing page
Route::get('/', [LandingController::class, 'index'])->name('landing');

// Contact form
Route::post('/contact', [LandingController::class, 'storeContact'])->name('contact.store');

// Authentication routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login')->middleware('guest');
Route::post('/login', [LoginController::class, 'login'])->middleware('guest');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// Client login routes (for patients)
Route::get('/client', [\App\Http\Controllers\ClientLoginController::class, 'showLoginForm'])->name('client.login')->middleware('guest');
Route::post('/client', [\App\Http\Controllers\ClientLoginController::class, 'login'])->middleware('guest');
Route::post('/client/logout', [\App\Http\Controllers\ClientLoginController::class, 'logout'])->name('client.logout')->middleware('auth');

// Client dashboard routes (for webview app)
Route::prefix('client')->name('client.')->middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [\App\Http\Controllers\ClientDashboardController::class, 'index'])->name('dashboard');
    
    // Device Management
    Route::get('/devices', [\App\Http\Controllers\ClientDeviceController::class, 'index'])->name('devices.index');
    Route::post('/devices', [\App\Http\Controllers\ClientDeviceController::class, 'store'])->name('devices.store');
    Route::delete('/devices/{device}', [\App\Http\Controllers\ClientDeviceController::class, 'destroy'])->name('devices.destroy');
    
    // Psychometric Assessments
    Route::get('/assessments', [\App\Http\Controllers\ClientPsychometricController::class, 'index'])->name('assessments.index');
    Route::get('/assessments/{assessment}', [\App\Http\Controllers\ClientPsychometricController::class, 'show'])->name('assessments.show');
    Route::post('/assessments/{assessment}/complete', [\App\Http\Controllers\ClientPsychometricController::class, 'complete'])->name('assessments.complete');
    
    // Contingency Management
    Route::get('/contingency', [\App\Http\Controllers\ClientContingencyController::class, 'index'])->name('contingency.index');
    Route::get('/contingency/{plan}', [\App\Http\Controllers\ClientContingencyController::class, 'show'])->name('contingency.show');
    Route::post('/contingency/{plan}/activate', [\App\Http\Controllers\ClientContingencyController::class, 'activate'])->name('contingency.activate');
    
    // Session Details (enhanced)
    Route::get('/sessions/{session}', [\App\Http\Controllers\PatientSessionController::class, 'show'])->name('sessions.show');
    
    // Progress Tracking
    Route::get('/progress', [\App\Http\Controllers\PatientProgressController::class, 'index'])->name('progress.index');
});

// Dashboard (protected)
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard')->middleware('auth');

// Patient routes (for patients logged in via web)
Route::prefix('patient')->name('patient.')->middleware(['auth'])->group(function () {
    Route::get('/dashboard', [PatientDashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', function () {
        return view('patient.profile');
    })->name('profile');
    Route::get('/sessions', [PatientSessionController::class, 'index'])->name('sessions.index');
    Route::get('/sessions/{id}', [PatientSessionController::class, 'show'])->name('sessions.show');
    Route::get('/resources', [PatientResourceController::class, 'patientIndex'])->name('resources.index');
    Route::get('/resources/{resource}/download', [PatientResourceController::class, 'patientDownload'])->name('resources.download');
    Route::get('/appointments', [PatientAppointmentController::class, 'index'])->name('appointments.index');
    Route::get('/assessments', [PatientAssessmentController::class, 'index'])->name('assessments.index');
    Route::get('/assessments/{id}', [PatientAssessmentController::class, 'show'])->name('assessments.show');
    Route::get('/progress', [PatientProgressController::class, 'index'])->name('progress.index');
    Route::get('/messages', [PatientMessageController::class, 'index'])->name('messages.index');
    Route::get('/medications', [PatientMedicationController::class, 'index'])->name('medications.index');
    Route::get('/journal', [PatientJournalController::class, 'index'])->name('journal.index');
    Route::get('/journal/create', function () {
        return view('patient.journal.create');
    })->name('journal.create');
    Route::post('/journal', [\App\Http\Controllers\PatientJournalController::class, 'store'])->name('journal.store');
    Route::put('/profile', [\App\Http\Controllers\PatientDashboardController::class, 'updateProfile'])->name('profile.update');
});

// Doctor profile and papers routes
Route::middleware(['auth', 'blade.role:admin,doctor'])->group(function () {
    Route::get('/doctors/{doctor?}/settings', [DoctorProfileController::class, 'edit'])
        ->name('doctors.settings');
    Route::put('/doctors/{doctor?}/settings', [DoctorProfileController::class, 'update'])
        ->name('doctors.settings.update');
    
    // Doctor papers routes
    Route::get('/doctors/{doctor?}/papers', [DoctorPaperController::class, 'index'])
        ->name('doctors.papers.index');
    Route::post('/doctors/papers', [DoctorPaperController::class, 'store'])
        ->name('doctors.papers.store');
    Route::get('/doctors/papers/{paper}', [DoctorPaperController::class, 'show'])
        ->name('doctors.papers.show');
    Route::get('/doctors/papers/{paper}/edit', [DoctorPaperController::class, 'edit'])
        ->name('doctors.papers.edit');
    Route::put('/doctors/papers/{paper}', [DoctorPaperController::class, 'update'])
        ->name('doctors.papers.update');
    Route::delete('/doctors/papers/{paper}', [DoctorPaperController::class, 'destroy'])
        ->name('doctors.papers.destroy');
    Route::get('/doctors/papers/{paper}/download', [DoctorPaperController::class, 'download'])
        ->name('doctors.papers.download');
    
    // Patient management routes
    Route::resource('patients', WebPatientController::class);
    
    // Session routes (nested under patients)
    Route::resource('patients.sessions', SessionController::class);
    
    // Session day routes
    Route::post('patients/{patient}/sessions/{session}/days', [SessionDayController::class, 'store'])
        ->name('patients.sessions.days.store');
    Route::put('patients/{patient}/sessions/{session}/days/{day}', [SessionDayController::class, 'update'])
        ->name('patients.sessions.days.update');
    Route::delete('patients/{patient}/sessions/{session}/days/{day}', [SessionDayController::class, 'destroy'])
        ->name('patients.sessions.days.destroy');
    
    // Patient Resources routes (nested under patients)
    Route::get('patients/{patient}/resources', [PatientResourceController::class, 'index'])
        ->name('patients.resources.index');
    Route::post('patients/{patient}/resources', [PatientResourceController::class, 'store'])
        ->name('patients.resources.store');
    Route::put('patients/{patient}/resources/{resource}', [PatientResourceController::class, 'update'])
        ->name('patients.resources.update');
    Route::delete('patients/{patient}/resources/{resource}', [PatientResourceController::class, 'destroy'])
        ->name('patients.resources.destroy');
    Route::get('patients/{patient}/resources/{resource}/download', [PatientResourceController::class, 'download'])
        ->name('patients.resources.download');
    
    // Doctor appointments
    Route::get('/doctors/appointments', [DoctorAppointmentController::class, 'index'])
        ->name('doctors.appointments.index');
    
    // Doctor messages
    Route::get('/doctors/messages', [DoctorMessageController::class, 'index'])
        ->name('doctors.messages.index');
    
    // Psychometric Scales Management
    Route::resource('psychometric-scales', PsychometricScaleController::class);
    Route::post('patients/{patient}/psychometric-assessments', [PsychometricAssessmentController::class, 'assign'])
        ->name('patients.psychometric.assign');
    Route::get('patients/{patient}/psychometric-assessments', [PsychometricAssessmentController::class, 'index'])
        ->name('patients.psychometric.index');
    Route::get('patients/{patient}/psychometric-assessments/{assessment}', [PsychometricAssessmentController::class, 'show'])
        ->name('patients.psychometric.show');
    
    // Contingency Management
    Route::resource('patients.contingency-plans', ContingencyPlanController::class);
    Route::post('patients/{patient}/contingency-plans/{contingencyPlan}/activate', [ContingencyPlanController::class, 'activate'])
        ->name('patients.contingency.activate');
});

// Admin routes
Route::prefix('admin')->name('admin.')->middleware(['auth', 'blade.role:admin'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    
    // Staff Management
    Route::resource('staff', StaffController::class);
    Route::post('staff/{staff}/toggle-status', [StaffController::class, 'toggleStatus'])->name('staff.toggle-status');
    Route::get('staff/assignments', [AssistantAssignmentController::class, 'index'])->name('staff.assignments.index');
    Route::post('staff/assignments', [AssistantAssignmentController::class, 'store'])->name('staff.assignments.store');
    Route::delete('staff/assignments/{assignment}', [AssistantAssignmentController::class, 'destroy'])->name('staff.assignments.destroy');
    
    // Patient Oversight
    Route::get('patients', [AdminPatientController::class, 'index'])->name('patients.index');
    Route::get('patients/{patient}', [AdminPatientController::class, 'show'])->name('patients.show');
    
    // Sessions Explorer
    Route::get('sessions', [AdminSessionController::class, 'index'])->name('sessions.index');
    Route::get('sessions/{session}', [AdminSessionController::class, 'show'])->name('sessions.show');
    
    // Analytics
    Route::get('analytics', [AnalyticsController::class, 'index'])->name('analytics.index');
    
    // AI Reports
    Route::get('ai-reports', [AiReportController::class, 'index'])->name('ai-reports.index');
    Route::get('ai-reports/{id}', [AiReportController::class, 'show'])->name('ai-reports.show');
    Route::post('ai-reports/generate-patient', [AiReportController::class, 'generatePatient'])->name('ai-reports.generate-patient');
    Route::post('ai-reports/generate-doctor', [AiReportController::class, 'generateDoctor'])->name('ai-reports.generate-doctor');
    Route::post('ai-reports/generate-clinic', [AiReportController::class, 'generateClinic'])->name('ai-reports.generate-clinic');
    Route::post('ai-reports/{id}/regenerate', [AiReportController::class, 'regenerate'])->name('ai-reports.regenerate');
    
    // Contact Inbox
    Route::get('contact', [ContactController::class, 'index'])->name('contact.index');
    Route::get('contact/{contact}', [ContactController::class, 'show'])->name('contact.show');
    Route::post('contact/{contact}/resolve', [ContactController::class, 'resolve'])->name('contact.resolve');
    Route::post('contact/{contact}/notes', [ContactController::class, 'addNotes'])->name('contact.notes');
});
