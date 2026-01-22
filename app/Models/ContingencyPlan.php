<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ContingencyPlan extends Model
{
    protected $table = 'contingency_plans';

    protected $fillable = [
        'patient_id',
        'patient_profile_id',
        'created_by_doctor_id',
        'title',
        'trigger_conditions',
        'actions',
        'emergency_contacts',
        'status',
        'activated_at',
    ];

    protected $casts = [
        'trigger_conditions' => 'array',
        'actions' => 'array',
        'emergency_contacts' => 'array',
        'activated_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the patient (from patients table).
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }

    /**
     * Get the patient profile (from patient_profiles table).
     */
    public function patientProfile(): BelongsTo
    {
        return $this->belongsTo(PatientProfile::class, 'patient_profile_id');
    }

    /**
     * Get the doctor who created this plan.
     */
    public function createdByDoctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_doctor_id');
    }

    /**
     * Get all activations of this plan.
     */
    public function activations(): HasMany
    {
        return $this->hasMany(ContingencyActivation::class, 'contingency_plan_id');
    }

    /**
     * Get the user (patient) through patient profile.
     */
    public function getUser()
    {
        if ($this->patient_profile_id && $this->patientProfile) {
            return $this->patientProfile->user;
        }
        if ($this->patient_id && $this->patient) {
            // Try to find user by email
            return \App\Models\User::where('email', $this->patient->email)->first();
        }
        return null;
    }

    /**
     * Get the user_id through patient profile.
     */
    public function getUserId()
    {
        $user = $this->getUser();
        return $user ? $user->id : null;
    }

    /**
     * Get the doctor_id through patient profile or created doctor.
     */
    public function getDoctorId()
    {
        // First try through created doctor
        if ($this->created_by_doctor_id) {
            return $this->created_by_doctor_id;
        }
        // Then try through patient profile
        if ($this->patient_profile_id && $this->patientProfile && $this->patientProfile->doctor_id) {
            return $this->patientProfile->doctor_id;
        }
        // Finally try through patient model
        if ($this->patient_id && $this->patient && $this->patient->doctor_id) {
            return $this->patient->doctor_id;
        }
        return null;
    }

    /**
     * Activate the contingency plan.
     */
    public function activate(string $triggeredBy, string $reason): ContingencyActivation
    {
        $activation = ContingencyActivation::create([
            'contingency_plan_id' => $this->id,
            'patient_id' => $this->patient_id,
            'patient_profile_id' => $this->patient_profile_id,
            'triggered_by' => $triggeredBy,
            'trigger_reason' => $reason,
            'activated_at' => now(),
        ]);

        $this->update([
            'activated_at' => now(),
        ]);

        return $activation;
    }

    /**
     * Check if plan is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}
