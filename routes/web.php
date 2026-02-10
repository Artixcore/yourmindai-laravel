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
use App\Http\Controllers\ClientRiskAssessmentController;

// Landing page
Route::get('/', [LandingController::class, 'index'])->name('landing');

// Contact form (rate limited: 10 per minute per IP)
Route::post('/contact', [LandingController::class, 'storeContact'])->name('contact.store')->middleware('throttle:10,1');

// Public appointment request (rate limited: 10 per minute per IP)
Route::post('/appointment-request', [\App\Http\Controllers\AppointmentRequestController::class, 'store'])->name('appointment-request.store')->middleware('throttle:10,1');

// Public Digital Wellbeing (no auth required)
Route::get('/wellbeing', [\App\Http\Controllers\ClientWellbeingController::class, 'publicIndex'])->name('wellbeing.public');

// Public signed download for session report PDF (share link)
Route::get('/report/{report}/download', [\App\Http\Controllers\ReportDownloadController::class, 'download'])
    ->name('report.download-public')
    ->middleware('signed');

// Public Articles (accessible to everyone)
Route::prefix('articles')->name('articles.')->group(function () {
    Route::get('/', [\App\Http\Controllers\ArticlePublicController::class, 'index'])->name('public.index');
    Route::get('/category/{slug}', [\App\Http\Controllers\ArticlePublicController::class, 'category'])->name('public.category');
    Route::get('/tag/{slug}', [\App\Http\Controllers\ArticlePublicController::class, 'tag'])->name('public.tag');
    Route::get('/search', [\App\Http\Controllers\ArticlePublicController::class, 'search'])->name('public.search');
    Route::post('/{article}/like', [\App\Http\Controllers\ArticlePublicController::class, 'like'])->name('public.like');
    Route::post('/{article}/comment', [\App\Http\Controllers\ArticlePublicController::class, 'comment'])->name('public.comment');
    Route::get('/{slug}', [\App\Http\Controllers\ArticlePublicController::class, 'show'])->name('public.show');
});

// Authentication routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login')->middleware('guest');
Route::post('/login', [LoginController::class, 'login'])->name('login.post')->middleware('guest');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// Client login routes (for patients)
Route::get('/client', [\App\Http\Controllers\ClientLoginController::class, 'showLoginForm'])->name('client.login')->middleware('guest');
Route::post('/client', [\App\Http\Controllers\ClientLoginController::class, 'login'])->name('client.login.post')->middleware('guest');
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
    
    // Digital Wellbeing
    Route::get('/wellbeing', [\App\Http\Controllers\ClientWellbeingController::class, 'index'])->name('wellbeing.index');
    
    // Lifestyle Monitoring
    Route::get('/lifestyle', [\App\Http\Controllers\ClientLifestyleController::class, 'index'])->name('lifestyle.index');
    Route::post('/lifestyle', [\App\Http\Controllers\ClientLifestyleController::class, 'store'])->name('lifestyle.store');
    
    // Client Notes (text + voice)
    Route::get('/notes', [\App\Http\Controllers\ClientNotesController::class, 'index'])->name('notes.index');
    Route::post('/notes', [\App\Http\Controllers\ClientNotesController::class, 'store'])->name('notes.store');
    
    // Resources (doctor-shared PDFs/videos) + App feedback
    Route::get('/resources', [\App\Http\Controllers\ClientResourceController::class, 'index'])->name('resources.index');
    Route::post('/feedback', [\App\Http\Controllers\ClientResourceController::class, 'storeFeedback'])->name('feedback.store');
    
    // Daily Journal (client panel)
    Route::get('/journal', [\App\Http\Controllers\ClientJournalController::class, 'index'])->name('journal.index');
    Route::get('/journal/create', function () {
        return view('client.journal.create');
    })->name('journal.create');
    Route::post('/journal', [\App\Http\Controllers\ClientJournalController::class, 'store'])->name('journal.store');
    
    // Session Details (enhanced)
    Route::get('/sessions/{session}', [\App\Http\Controllers\PatientSessionController::class, 'show'])->name('sessions.show');
    
    // Progress Tracking
    Route::get('/progress', [\App\Http\Controllers\PatientProgressController::class, 'index'])->name('progress.index');
    
    // Tasks/Todos
    Route::get('/tasks', [\App\Http\Controllers\PatientTaskController::class, 'index'])->name('client.tasks.index');
    Route::get('/tasks/{task}', [\App\Http\Controllers\PatientTaskController::class, 'show'])->name('client.tasks.show');
    Route::post('/tasks/{task}/complete', [\App\Http\Controllers\PatientTaskController::class, 'complete'])->name('client.tasks.complete');
    
    // Reviews
    Route::get('/reviews', [\App\Http\Controllers\ClientReviewController::class, 'index'])->name('reviews.index');
    Route::get('/reviews/create', [\App\Http\Controllers\ClientReviewController::class, 'create'])->name('reviews.create');
    Route::post('/reviews', [\App\Http\Controllers\ClientReviewController::class, 'store'])->name('reviews.store');
    Route::get('/reviews/{review}', [\App\Http\Controllers\ClientReviewController::class, 'show'])->name('reviews.show');
    Route::get('/reviews/{review}/edit', [\App\Http\Controllers\ClientReviewController::class, 'edit'])->name('reviews.edit');
    Route::put('/reviews/{review}', [\App\Http\Controllers\ClientReviewController::class, 'update'])->name('reviews.update');
    Route::get('/reviews/check-eligibility', [\App\Http\Controllers\ClientReviewController::class, 'checkEligibility'])->name('reviews.check-eligibility');
    Route::get('/doctors/{doctor}/reviews', [\App\Http\Controllers\ClientReviewController::class, 'doctorReviews'])->name('doctors.reviews');
    
    // General Assessments (Phase 2)
    Route::get('/general-assessments', [\App\Http\Controllers\ClientGeneralAssessmentController::class, 'index'])->name('general-assessment.index');
    Route::get('/general-assessments/{assessment}', [\App\Http\Controllers\ClientGeneralAssessmentController::class, 'show'])->name('general-assessment.show');
    Route::post('/general-assessments/{assessment}/submit', [\App\Http\Controllers\ClientGeneralAssessmentController::class, 'submit'])->name('general-assessment.submit');
    Route::get('/general-assessments/{assessment}/result', [\App\Http\Controllers\ClientGeneralAssessmentController::class, 'result'])->name('general-assessment.result');
    
    // Homework & Techniques (Phase 2)
    Route::get('/homework', [\App\Http\Controllers\ClientHomeworkController::class, 'index'])->name('homework.index');
    Route::get('/homework/{homework}', [\App\Http\Controllers\ClientHomeworkController::class, 'show'])->name('homework.show');
    Route::post('/homework/{homework}/complete', [\App\Http\Controllers\ClientHomeworkController::class, 'complete'])->name('homework.complete');
    
    // Mood Tracking
    Route::get('/mood', [\App\Http\Controllers\ClientMoodController::class, 'index'])->name('mood.index');
    Route::post('/mood', [\App\Http\Controllers\ClientMoodController::class, 'store'])->name('mood.store');
    
    // Sleep Tracking
    Route::get('/sleep', [\App\Http\Controllers\ClientSleepController::class, 'index'])->name('sleep.index');
    Route::post('/sleep', [\App\Http\Controllers\ClientSleepController::class, 'store'])->name('sleep.store');
    
    // Exercise Tracking
    Route::get('/exercise', [\App\Http\Controllers\ClientExerciseController::class, 'index'])->name('exercise.index');
    Route::post('/exercise', [\App\Http\Controllers\ClientExerciseController::class, 'store'])->name('exercise.store');
    
    // Routine Management
    Route::get('/routine', [\App\Http\Controllers\ClientRoutineController::class, 'index'])->name('routine.index');
    Route::post('/routine/{item}/log', [\App\Http\Controllers\ClientRoutineController::class, 'logItem'])->name('routine.log');
    
    // Risk Assessments (Phase 5)
    Route::get('/risk-assessments', [\App\Http\Controllers\ClientRiskAssessmentController::class, 'index'])->name('risk-assessments.index');
    Route::get('/risk-assessments/{assessment}', [\App\Http\Controllers\ClientRiskAssessmentController::class, 'show'])->name('risk-assessments.show');
});

// Dashboard (protected)
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard')->middleware('auth');

// Parent routes (Phase 1)
Route::prefix('parent')->name('parent.')->middleware(['auth'])->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\ParentDashboardController::class, 'index'])->name('dashboard');
    Route::get('/child/{patient}', [\App\Http\Controllers\ParentDashboardController::class, 'showChild'])->name('child.show');
    Route::get('/child/{patient}/homework', [\App\Http\Controllers\ParentDashboardController::class, 'showChildHomework'])->name('child.homework');
    Route::get('/child/{patient}/sessions', [\App\Http\Controllers\ParentDashboardController::class, 'showChildSessions'])->name('child.sessions');
    
    // Feedback
    Route::post('/feedback/{feedbackable_type}/{feedbackable_id}', [\App\Http\Controllers\FeedbackController::class, 'store'])->name('feedback.store');
    
    // Practice Progression
    Route::post('/progression/{progressionable_type}/{progressionable_id}', [\App\Http\Controllers\PracticeProgressionController::class, 'store'])->name('progression.store');
    
    // Permissions
    Route::get('/permissions', [\App\Http\Controllers\ParentPermissionController::class, 'index'])->name('permissions.index');
    Route::post('/permissions', [\App\Http\Controllers\ParentPermissionController::class, 'update'])->name('permissions.update');
});

// Others/Experts routes (Phase 1)
Route::prefix('others')->name('others.')->middleware(['auth'])->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\OthersController::class, 'index'])->name('dashboard');
    Route::get('/clients', [\App\Http\Controllers\OthersController::class, 'clients'])->name('clients.index');
    Route::get('/clients/{patient}', [\App\Http\Controllers\OthersController::class, 'showClient'])->name('clients.show');
    Route::get('/clients/{patient}/homework', [\App\Http\Controllers\OthersController::class, 'showClientHomework'])->name('clients.homework');
    
    // Feedback
    Route::post('/feedback/{feedbackable_type}/{feedbackable_id}', [\App\Http\Controllers\FeedbackController::class, 'store'])->name('feedback.store');
    
    // Practice Progression
    Route::post('/progression/{progressionable_type}/{progressionable_id}', [\App\Http\Controllers\PracticeProgressionController::class, 'store'])->name('progression.store');
    
    // Referrals
    Route::get('/referrals', [\App\Http\Controllers\OthersController::class, 'referrals'])->name('referrals.index');
    Route::get('/referrals/{referral}', [\App\Http\Controllers\OthersController::class, 'showReferral'])->name('referrals.show');
    Route::post('/referrals/{referral}/respond', [\App\Http\Controllers\OthersController::class, 'respondToReferral'])->name('referrals.respond');
});

// Writer routes (for article writers - admin, doctors, or writers)
Route::prefix('writer')->name('writer.')->middleware(['auth', \App\Http\Middleware\WriterMiddleware::class])->group(function () {
    // Dashboard
    Route::get('/dashboard', [\App\Http\Controllers\Writer\DashboardController::class, 'index'])->name('dashboard');
    
    // Articles
    Route::resource('articles', \App\Http\Controllers\Writer\ArticleController::class);
    Route::post('articles/{article}/submit', [\App\Http\Controllers\Writer\ArticleController::class, 'submitForReview'])->name('articles.submit');
    Route::get('articles/{article}/preview', [\App\Http\Controllers\Writer\ArticleController::class, 'preview'])->name('articles.preview');
    Route::post('articles/upload-image', [\App\Http\Controllers\Writer\ArticleController::class, 'uploadImage'])->name('articles.upload-image');
    
    // Earnings
    Route::get('/earnings', [\App\Http\Controllers\Writer\EarningsController::class, 'index'])->name('earnings.index');
    Route::get('/earnings/{period}', [\App\Http\Controllers\Writer\EarningsController::class, 'show'])->name('earnings.show');
});

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
    
    // Tasks/Todos
    Route::get('/tasks', [\App\Http\Controllers\PatientTaskController::class, 'index'])->name('patient.tasks.index');
    Route::get('/tasks/{task}', [\App\Http\Controllers\PatientTaskController::class, 'show'])->name('patient.tasks.show');
    Route::post('/tasks/{task}/complete', [\App\Http\Controllers\PatientTaskController::class, 'complete'])->name('patient.tasks.complete');
});

// Doctor profile and papers routes
Route::middleware(['auth', 'blade.role:admin,doctor'])->group(function () {
    Route::get('doctors/{doctor?}/settings', [DoctorProfileController::class, 'edit'])
        ->name('doctors.settings');
    Route::put('doctors/{doctor?}/settings', [DoctorProfileController::class, 'update'])
        ->name('doctors.settings.update');
    
    // Doctor papers routes
    Route::get('doctors/{doctor?}/papers', [DoctorPaperController::class, 'index'])
        ->name('doctors.papers.index');
    Route::post('doctors/papers', [DoctorPaperController::class, 'store'])
        ->name('doctors.papers.store');
    Route::get('doctors/papers/{paper}', [DoctorPaperController::class, 'show'])
        ->name('doctors.papers.show');
    Route::get('doctors/papers/{paper}/edit', [DoctorPaperController::class, 'edit'])
        ->name('doctors.papers.edit');
    Route::put('doctors/papers/{paper}', [DoctorPaperController::class, 'update'])
        ->name('doctors.papers.update');
    Route::delete('doctors/papers/{paper}', [DoctorPaperController::class, 'destroy'])
        ->name('doctors.papers.destroy');
    Route::get('doctors/papers/{paper}/download', [DoctorPaperController::class, 'download'])
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
    Route::get('doctors/appointments', [DoctorAppointmentController::class, 'index'])
        ->name('doctors.appointments.index');
    
    // Doctor sessions
    Route::get('doctors/sessions', [\App\Http\Controllers\DoctorSessionController::class, 'index'])
        ->name('doctors.sessions.index');
    
    // Doctor messages
    Route::get('doctors/messages', [DoctorMessageController::class, 'index'])
        ->name('doctors.messages.index');
    
    // Doctor Appointment Requests (requests assigned to them)
    Route::get('doctors/appointment-requests', [\App\Http\Controllers\AppointmentRequestController::class, 'index'])
        ->name('doctors.appointment-requests.index');
    Route::get('doctors/appointment-requests/{appointmentRequest}', [\App\Http\Controllers\AppointmentRequestController::class, 'show'])
        ->name('doctors.appointment-requests.show');
    
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
    
    // General Assessments Management (Phase 3)
    Route::get('patients/{patient}/general-assessments', [\App\Http\Controllers\Doctor\GeneralAssessmentController::class, 'index'])
        ->name('patients.general-assessments.index');
    Route::get('patients/{patient}/general-assessments/create', [\App\Http\Controllers\Doctor\GeneralAssessmentController::class, 'create'])
        ->name('patients.general-assessments.create');
    Route::post('patients/{patient}/general-assessments', [\App\Http\Controllers\Doctor\GeneralAssessmentController::class, 'store'])
        ->name('patients.general-assessments.store');
    Route::get('patients/{patient}/general-assessments/{assessment}', [\App\Http\Controllers\Doctor\GeneralAssessmentController::class, 'show'])
        ->name('patients.general-assessments.show');
    
    // Homework Management (Phase 3)
    Route::get('patients/{patient}/homework', [\App\Http\Controllers\Doctor\HomeworkController::class, 'index'])
        ->name('patients.homework.index');
    Route::get('patients/{patient}/homework/create', [\App\Http\Controllers\Doctor\HomeworkController::class, 'create'])
        ->name('patients.homework.create');
    Route::post('patients/{patient}/homework', [\App\Http\Controllers\Doctor\HomeworkController::class, 'store'])
        ->name('patients.homework.store');
    Route::get('patients/{patient}/homework/{homework}', [\App\Http\Controllers\Doctor\HomeworkController::class, 'show'])
        ->name('patients.homework.show');
    Route::put('patients/{patient}/homework/{homework}', [\App\Http\Controllers\Doctor\HomeworkController::class, 'update'])
        ->name('patients.homework.update');
    
    // Goals Management
    Route::get('patients/{patient}/goals', [\App\Http\Controllers\Doctor\GoalController::class, 'index'])
        ->name('patients.goals.index');
    Route::get('patients/{patient}/goals/create', [\App\Http\Controllers\Doctor\GoalController::class, 'create'])
        ->name('patients.goals.create');
    Route::post('patients/{patient}/goals', [\App\Http\Controllers\Doctor\GoalController::class, 'store'])
        ->name('patients.goals.store');
    Route::get('patients/{patient}/goals/{goal}/edit', [\App\Http\Controllers\Doctor\GoalController::class, 'edit'])
        ->name('patients.goals.edit');
    Route::put('patients/{patient}/goals/{goal}', [\App\Http\Controllers\Doctor\GoalController::class, 'update'])
        ->name('patients.goals.update');
    Route::delete('patients/{patient}/goals/{goal}', [\App\Http\Controllers\Doctor\GoalController::class, 'destroy'])
        ->name('patients.goals.destroy');
    
    // Routine Management (Phase 3)
    Route::get('patients/{patient}/routines', [\App\Http\Controllers\Doctor\RoutineController::class, 'index'])
        ->name('patients.routines.index');
    Route::get('patients/{patient}/routines/create', [\App\Http\Controllers\Doctor\RoutineController::class, 'create'])
        ->name('patients.routines.create');
    Route::post('patients/{patient}/routines', [\App\Http\Controllers\Doctor\RoutineController::class, 'store'])
        ->name('patients.routines.store');
    Route::get('patients/{patient}/routines/{routine}', [\App\Http\Controllers\Doctor\RoutineController::class, 'show'])
        ->name('patients.routines.show');
    Route::post('patients/{patient}/routines/{routine}/toggle', [\App\Http\Controllers\Doctor\RoutineController::class, 'toggleActive'])
        ->name('patients.routines.toggle');
    
    // Tracking Logs Monitoring (Phase 3)
    Route::get('patients/{patient}/tracking', [\App\Http\Controllers\Doctor\TrackingLogController::class, 'index'])
        ->name('patients.tracking.index');
    Route::get('patients/{patient}/tracking/mood', [\App\Http\Controllers\Doctor\TrackingLogController::class, 'mood'])
        ->name('patients.tracking.mood');
    Route::get('patients/{patient}/tracking/sleep', [\App\Http\Controllers\Doctor\TrackingLogController::class, 'sleep'])
        ->name('patients.tracking.sleep');
    Route::get('patients/{patient}/tracking/exercise', [\App\Http\Controllers\Doctor\TrackingLogController::class, 'exercise'])
        ->name('patients.tracking.exercise');
    
    // Risk Assessment Management (Phase 5)
    Route::get('patients/{patient}/risk-assessments', [\App\Http\Controllers\Doctor\RiskAssessmentController::class, 'index'])
        ->name('patients.risk-assessments.index');
    Route::get('patients/{patient}/risk-assessments/create', [\App\Http\Controllers\Doctor\RiskAssessmentController::class, 'create'])
        ->name('patients.risk-assessments.create');
    Route::post('patients/{patient}/risk-assessments', [\App\Http\Controllers\Doctor\RiskAssessmentController::class, 'store'])
        ->name('patients.risk-assessments.store');
    Route::get('patients/{patient}/risk-assessments/{assessment}', [\App\Http\Controllers\Doctor\RiskAssessmentController::class, 'show'])
        ->name('patients.risk-assessments.show');
    Route::put('patients/{patient}/risk-assessments/{assessment}', [\App\Http\Controllers\Doctor\RiskAssessmentController::class, 'update'])
        ->name('patients.risk-assessments.update');
    
    // Doctor Reviews
    Route::get('doctors/reviews', [\App\Http\Controllers\DoctorReviewController::class, 'index'])
        ->name('doctor.reviews.index');
    Route::get('doctors/reviews/analytics', [\App\Http\Controllers\DoctorReviewController::class, 'analytics'])
        ->name('doctor.reviews.analytics');
    Route::get('doctors/reviews/{review}', [\App\Http\Controllers\DoctorReviewController::class, 'show'])
        ->name('doctor.reviews.show');
    
    // Doctor - Feedback Management (Phase 6)
    Route::get('patients/{patient}/feedback', [\App\Http\Controllers\Doctor\FeedbackController::class, 'index'])
        ->name('patients.feedback.index');
    Route::get('patients/{patient}/feedback/{feedback}', [\App\Http\Controllers\Doctor\FeedbackController::class, 'show'])
        ->name('patients.feedback.show');
    Route::post('feedback/{feedback}/respond', [\App\Http\Controllers\Doctor\FeedbackController::class, 'respond'])
        ->name('feedback.respond');
    
    // Doctor - Practice Progression Management (Phase 6)
    Route::get('patients/{patient}/practice-progressions', [\App\Http\Controllers\Doctor\PracticeProgressionController::class, 'index'])
        ->name('patients.practice-progressions.index');
    Route::get('patients/{patient}/practice-progressions/{progression}', [\App\Http\Controllers\Doctor\PracticeProgressionController::class, 'show'])
        ->name('patients.practice-progressions.show');
    Route::get('patients/{patient}/practice-progressions-analytics', [\App\Http\Controllers\Doctor\PracticeProgressionController::class, 'analytics'])
        ->name('patients.practice-progressions.analytics');
    
    // Doctor - Session Reports (Phase 6)
    Route::resource('session-reports', \App\Http\Controllers\Doctor\SessionReportController::class);
    Route::post('session-reports/{report}/finalize', [\App\Http\Controllers\Doctor\SessionReportController::class, 'finalize'])
        ->name('session-reports.finalize');
    Route::post('session-reports/{report}/share', [\App\Http\Controllers\Doctor\SessionReportController::class, 'share'])
        ->name('session-reports.share');
    Route::post('session-reports/{report}/generate-pdf', [\App\Http\Controllers\Doctor\SessionReportController::class, 'generatePdf'])
        ->name('session-reports.generate-pdf');
    Route::get('session-reports/{report}/download-pdf', [\App\Http\Controllers\Doctor\SessionReportController::class, 'downloadPdf'])
        ->name('session-reports.download-pdf');
    Route::get('session-reports/{report}/share-link', [\App\Http\Controllers\Doctor\SessionReportController::class, 'shareLink'])
        ->name('session-reports.share-link');
    Route::post('session-reports/{report}/send-email', [\App\Http\Controllers\Doctor\SessionReportController::class, 'sendViaEmail'])
        ->name('session-reports.send-email');
    
    // Doctor - Psychometric Assessments (Phase 6 - Enhanced)
    Route::get('patients/{patient}/psychometric-compare', [\App\Http\Controllers\Doctor\PsychometricAssessmentController::class, 'compare'])
        ->name('patients.psychometric.compare');
    
    // Doctor - Patient Tracking Dashboard (Phase 6)
    Route::get('patients/{patient}/tracking-overview', [\App\Http\Controllers\Doctor\PatientTrackingController::class, 'overview'])
        ->name('patients.tracking.overview');
    Route::get('patients/{patient}/tracking/timeline', [\App\Http\Controllers\Doctor\PatientTrackingController::class, 'timeline'])
        ->name('patients.tracking.timeline');
    Route::get('patients/{patient}/tracking/compliance', [\App\Http\Controllers\Doctor\PatientTrackingController::class, 'compliance'])
        ->name('patients.tracking.compliance');
    
    // Doctor - Patient Device Monitoring (Phase 6)
    Route::get('patients/{patient}/devices', [\App\Http\Controllers\Doctor\PatientDeviceController::class, 'index'])
        ->name('patients.devices.index');
    Route::get('patients/{patient}/devices/{device}', [\App\Http\Controllers\Doctor\PatientDeviceController::class, 'show'])
        ->name('patients.devices.show');
    
    // Doctor - Task Management (Phase 6)
    Route::resource('tasks', \App\Http\Controllers\Doctor\TaskManagementController::class)->except(['index']);
    Route::get('tasks', [\App\Http\Controllers\Doctor\TaskManagementController::class, 'index'])->name('tasks.index');
    Route::get('patients/{patient}/tasks', [\App\Http\Controllers\Doctor\TaskManagementController::class, 'patientTasks'])
        ->name('patients.tasks.index');
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
    Route::post('patients/{patient}/transfer', [AdminPatientController::class, 'transfer'])->name('patients.transfer');
    
    // Note: Admin can access patient sessions through shared routes in the admin/doctor middleware group
    // Routes: patients.sessions.index, patients.sessions.create, etc. (accessible to both admin and doctor)
    
    // Psychometric Assessments (Admin)
    Route::get('patients/{patient}/psychometric-assessments', [\App\Http\Controllers\PsychometricAssessmentController::class, 'index'])
        ->name('patients.psychometric.index');
    Route::post('patients/{patient}/psychometric-assessments', [\App\Http\Controllers\PsychometricAssessmentController::class, 'assign'])
        ->name('patients.psychometric.assign');
    Route::get('patients/{patient}/psychometric-assessments/{assessment}', [\App\Http\Controllers\PsychometricAssessmentController::class, 'show'])
        ->name('patients.psychometric.show');
    
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
    
    // Appointment Requests
    Route::get('appointment-requests', [\App\Http\Controllers\AppointmentRequestController::class, 'index'])->name('appointment-requests.index');
    Route::get('appointment-requests/{appointmentRequest}', [\App\Http\Controllers\AppointmentRequestController::class, 'show'])->name('appointment-requests.show');
    Route::post('appointment-requests/{appointmentRequest}/approve', [\App\Http\Controllers\AppointmentRequestController::class, 'approve'])->name('appointment-requests.approve');
    Route::post('appointment-requests/{appointmentRequest}/reject', [\App\Http\Controllers\AppointmentRequestController::class, 'reject'])->name('appointment-requests.reject');
    Route::get('appointment-requests/{appointmentRequest}/create-patient', [\App\Http\Controllers\AppointmentRequestController::class, 'createPatient'])->name('appointment-requests.create-patient');
    Route::post('appointment-requests/{appointmentRequest}/create-patient', [\App\Http\Controllers\AppointmentRequestController::class, 'storePatient'])->name('appointment-requests.store-patient');
    
    // Review Management
    Route::resource('reviews', \App\Http\Controllers\Admin\ReviewController::class)->only(['index', 'show']);
    Route::post('reviews/{review}/moderate', [\App\Http\Controllers\Admin\ReviewController::class, 'moderate'])->name('reviews.moderate');
    Route::get('analytics/reviews', [\App\Http\Controllers\Admin\ReviewController::class, 'analytics'])->name('analytics.reviews');
    
    // Review Question Management
    Route::resource('review-questions', \App\Http\Controllers\Admin\ReviewQuestionController::class);
    Route::post('review-questions/reorder', [\App\Http\Controllers\Admin\ReviewQuestionController::class, 'reorder'])->name('review-questions.reorder');
    
    // Risk Assessment Overview (Phase 5)
    Route::get('risk-assessments', [\App\Http\Controllers\Admin\RiskAssessmentController::class, 'index'])->name('risk-assessments.index');
    Route::get('risk-assessments/analytics', [\App\Http\Controllers\Admin\RiskAssessmentController::class, 'analytics'])->name('risk-assessments.analytics');
    Route::get('risk-assessments/high-risk', [\App\Http\Controllers\Admin\RiskAssessmentController::class, 'highRisk'])->name('risk-assessments.high-risk');
    Route::get('risk-assessments/{assessment}', [\App\Http\Controllers\Admin\RiskAssessmentController::class, 'show'])->name('risk-assessments.show');
    
    // Feedback Management (Phase 6)
    Route::resource('feedback', \App\Http\Controllers\Admin\FeedbackController::class)->only(['index', 'show', 'destroy']);
    Route::get('feedback-export', [\App\Http\Controllers\Admin\FeedbackController::class, 'export'])->name('feedback.export');
    
    // Practice Progression Management (Phase 6)
    Route::resource('practice-progressions', \App\Http\Controllers\Admin\PracticeProgressionController::class)->only(['index', 'show']);
    Route::get('practice-progressions-analytics', [\App\Http\Controllers\Admin\PracticeProgressionController::class, 'analytics'])->name('practice-progressions.analytics');
    Route::get('practice-progressions-export', [\App\Http\Controllers\Admin\PracticeProgressionController::class, 'export'])->name('practice-progressions.export');
    
    // Session Reports Management (Phase 6)
    Route::resource('session-reports', \App\Http\Controllers\Admin\SessionReportController::class)->only(['index', 'show']);
    Route::get('session-reports-analytics', [\App\Http\Controllers\Admin\SessionReportController::class, 'analytics'])->name('session-reports.analytics');
    
    // Parent Permission Management (Phase 6)
    Route::resource('parent-permissions', \App\Http\Controllers\Admin\ParentPermissionController::class);
    
    // Psychometric Scales Management (Phase 6 - Enhanced)
    Route::resource('psychometric-scales', \App\Http\Controllers\Admin\PsychometricScaleController::class);
    Route::post('psychometric-scales/{psychometricScale}/toggle', [\App\Http\Controllers\Admin\PsychometricScaleController::class, 'toggleActive'])->name('psychometric-scales.toggle');
    
    // Patient Device Management (Phase 6)
    Route::get('devices', [\App\Http\Controllers\Admin\PatientDeviceController::class, 'index'])->name('devices.index');
    Route::get('devices/analytics', [\App\Http\Controllers\Admin\PatientDeviceController::class, 'analytics'])->name('devices.analytics');
    Route::get('devices/{device}', [\App\Http\Controllers\Admin\PatientDeviceController::class, 'show'])->name('devices.show');
    Route::delete('devices/{device}', [\App\Http\Controllers\Admin\PatientDeviceController::class, 'destroy'])->name('devices.destroy');
    
    // Enhanced Tracking Overview (Phase 6)
    Route::get('tracking/all', [\App\Http\Controllers\Admin\TrackingOverviewController::class, 'allTracking'])->name('tracking.all');
    Route::get('tracking/by-type', [\App\Http\Controllers\Admin\TrackingOverviewController::class, 'trackingByType'])->name('tracking.by-type');
    Route::get('tracking/compliance', [\App\Http\Controllers\Admin\TrackingOverviewController::class, 'complianceReport'])->name('tracking.compliance');
    Route::get('tracking/patient-comparison', [\App\Http\Controllers\Admin\TrackingOverviewController::class, 'patientComparison'])->name('tracking.patient-comparison');
    
    // Task Management (Phase 6)
    Route::get('tasks', [\App\Http\Controllers\Admin\TaskManagementController::class, 'index'])->name('tasks.index');
    Route::get('tasks/analytics', [\App\Http\Controllers\Admin\TaskManagementController::class, 'analytics'])->name('tasks.analytics');
    Route::get('tasks/{task}', [\App\Http\Controllers\Admin\TaskManagementController::class, 'show'])->name('tasks.show');
    
    // Article Management
    Route::prefix('articles')->name('articles.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\ArticleController::class, 'index'])->name('index');
        Route::get('/{article}', [\App\Http\Controllers\Admin\ArticleController::class, 'show'])->name('show');
        Route::post('/{article}/approve', [\App\Http\Controllers\Admin\ArticleController::class, 'approve'])->name('approve');
        Route::post('/{article}/reject', [\App\Http\Controllers\Admin\ArticleController::class, 'reject'])->name('reject');
        Route::post('/{article}/publish', [\App\Http\Controllers\Admin\ArticleController::class, 'publish'])->name('publish');
        Route::post('/{article}/unpublish', [\App\Http\Controllers\Admin\ArticleController::class, 'unpublish'])->name('unpublish');
        Route::post('/{article}/feature', [\App\Http\Controllers\Admin\ArticleController::class, 'feature'])->name('feature');
        Route::post('/{article}/unfeature', [\App\Http\Controllers\Admin\ArticleController::class, 'unfeature'])->name('unfeature');
        Route::post('/reorder', [\App\Http\Controllers\Admin\ArticleController::class, 'reorder'])->name('reorder');
    });
    
    // Article Categories
    Route::resource('article-categories', \App\Http\Controllers\Admin\ArticleCategoryController::class);
    
    // Article Comments
    Route::get('article-comments', [\App\Http\Controllers\Admin\ArticleCommentController::class, 'index'])->name('article-comments.index');
    Route::post('article-comments/{comment}/approve', [\App\Http\Controllers\Admin\ArticleCommentController::class, 'approve'])->name('article-comments.approve');
    Route::post('article-comments/{comment}/reject', [\App\Http\Controllers\Admin\ArticleCommentController::class, 'reject'])->name('article-comments.reject');
    Route::delete('article-comments/{comment}', [\App\Http\Controllers\Admin\ArticleCommentController::class, 'destroy'])->name('article-comments.destroy');
    
    // Article Earnings
    Route::get('article-earnings', [\App\Http\Controllers\Admin\ArticleEarningsController::class, 'index'])->name('article-earnings.index');
    Route::post('article-earnings/calculate', [\App\Http\Controllers\Admin\ArticleEarningsController::class, 'calculate'])->name('article-earnings.calculate');
    Route::post('article-earnings/{earning}/paid', [\App\Http\Controllers\Admin\ArticleEarningsController::class, 'markAsPaid'])->name('article-earnings.paid');
    
    // General Assessments Overview (Phase 4)
    Route::get('general-assessments', [\App\Http\Controllers\Admin\GeneralAssessmentController::class, 'index'])->name('general-assessments.index');
    Route::get('general-assessments/{assessment}', [\App\Http\Controllers\Admin\GeneralAssessmentController::class, 'show'])->name('general-assessments.show');
    Route::get('general-assessments/export', [\App\Http\Controllers\Admin\GeneralAssessmentController::class, 'export'])->name('general-assessments.export');
    
    // Homework Overview (Phase 4)
    Route::get('homework', [\App\Http\Controllers\Admin\HomeworkTemplateController::class, 'index'])->name('homework.index');
    Route::get('homework/{homework}', [\App\Http\Controllers\Admin\HomeworkTemplateController::class, 'show'])->name('homework.show');
    Route::get('homework/analytics', [\App\Http\Controllers\Admin\HomeworkTemplateController::class, 'analytics'])->name('homework.analytics');
    
    // Tracking Logs Overview (Phase 4)
    Route::get('tracking', [\App\Http\Controllers\Admin\TrackingOverviewController::class, 'index'])->name('tracking.index');
    Route::get('tracking/mood', [\App\Http\Controllers\Admin\TrackingOverviewController::class, 'mood'])->name('tracking.mood');
    Route::get('tracking/sleep', [\App\Http\Controllers\Admin\TrackingOverviewController::class, 'sleep'])->name('tracking.sleep');
    Route::get('tracking/exercise', [\App\Http\Controllers\Admin\TrackingOverviewController::class, 'exercise'])->name('tracking.exercise');
});
