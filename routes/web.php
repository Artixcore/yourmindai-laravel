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

// Landing page
Route::get('/', [LandingController::class, 'index'])->name('landing');

// Contact form
Route::post('/contact', [LandingController::class, 'storeContact'])->name('contact.store');

// Authentication routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login')->middleware('guest');
Route::post('/login', [LoginController::class, 'login'])->middleware('guest');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// Dashboard (protected)
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard')->middleware('auth');

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
});
