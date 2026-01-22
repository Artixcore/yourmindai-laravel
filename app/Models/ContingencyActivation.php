<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContingencyActivation extends Model
{
    protected $table = 'contingency_activations';

    protected $fillable = [
        'contingency_plan_id',
        'patient_id',
        'patient_profile_id',
        'triggered_by',
        'trigger_reason',
        'actions_taken',
        'outcome',
        'activated_at',
        'resolved_at',
    ];

    protected $casts = [
        'actions_taken' => 'array',
        'activated_at' => 'datetime',
        'resolved_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the contingency plan.
     */
    public function contingencyPlan(): BelongsTo
    {
        return $this->belongsTo(ContingencyPlan::class, 'contingency_plan_id');
    }

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
     * Resolve the activation with outcome.
     */
    public function resolve(string $outcome): void
    {
        $this->update([
            'outcome' => $outcome,
            'resolved_at' => now(),
        ]);
    }
}
