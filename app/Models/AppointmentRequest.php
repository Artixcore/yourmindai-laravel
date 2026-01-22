<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AppointmentRequest extends Model
{
    protected $table = 'appointment_requests';

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'address',
        'preferred_date',
        'preferred_time',
        'status',
        'notes',
        'doctor_id',
        'patient_id',
        'patient_profile_id',
    ];

    protected $casts = [
        'preferred_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the doctor assigned to this request.
     */
    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    /**
     * Get the patient created from this request (from patients table).
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }

    /**
     * Get the patient profile created from this request.
     */
    public function patientProfile(): BelongsTo
    {
        return $this->belongsTo(PatientProfile::class, 'patient_profile_id');
    }

    /**
     * Get full name.
     */
    public function getFullNameAttribute(): string
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    /**
     * Approve the appointment request.
     */
    public function approve(?int $doctorId = null): void
    {
        $this->update([
            'status' => 'approved',
            'doctor_id' => $doctorId ?? $this->doctor_id,
        ]);
    }

    /**
     * Reject the appointment request.
     */
    public function reject(?string $reason = null): void
    {
        $this->update([
            'status' => 'rejected',
            'notes' => $this->notes ? $this->notes . "\n\nRejection reason: " . $reason : ($reason ? "Rejection reason: " . $reason : null),
        ]);
    }

    /**
     * Mark request as converted (patient created).
     */
    public function markAsConverted(?int $patientId = null, ?int $patientProfileId = null): void
    {
        $this->update([
            'status' => 'converted',
            'patient_id' => $patientId ?? $this->patient_id,
            'patient_profile_id' => $patientProfileId ?? $this->patient_profile_id,
        ]);
    }

    /**
     * Check if request is pending.
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if request is approved.
     */
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    /**
     * Check if request is converted.
     */
    public function isConverted(): bool
    {
        return $this->status === 'converted';
    }
}
