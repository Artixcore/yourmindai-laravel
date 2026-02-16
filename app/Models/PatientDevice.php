<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PatientDevice extends Model
{
    protected $table = 'patient_devices';

    protected $fillable = [
        'patient_id',
        'patient_profile_id',
        'device_name',
        'device_type',
        'device_identifier',
        'device_source',
        'os_type',
        'os_version',
        'app_version',
        'notes',
        'last_active_at',
        'is_active',
        'registered_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_active_at' => 'datetime',
        'registered_at' => 'datetime',
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
     * Check if device is active.
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }

    /**
     * Mark device as active.
     */
    public function markActive(): void
    {
        $this->update([
            'is_active' => true,
            'last_active_at' => now(),
        ]);
    }

    /**
     * Mark device as inactive.
     */
    public function markInactive(): void
    {
        $this->update([
            'is_active' => false,
        ]);
    }
}
