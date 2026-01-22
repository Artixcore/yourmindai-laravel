<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PsychometricAssessment extends Model
{
    protected $table = 'psychometric_assessments';

    protected $fillable = [
        'patient_id',
        'patient_profile_id',
        'scale_id',
        'assigned_by_doctor_id',
        'status',
        'responses',
        'total_score',
        'sub_scores',
        'interpretation',
        'completed_at',
        'assigned_at',
    ];

    protected $casts = [
        'responses' => 'array',
        'sub_scores' => 'array',
        'total_score' => 'integer',
        'completed_at' => 'datetime',
        'assigned_at' => 'datetime',
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
     * Get the psychometric scale.
     */
    public function scale(): BelongsTo
    {
        return $this->belongsTo(PsychometricScale::class, 'scale_id');
    }

    /**
     * Get the doctor who assigned this assessment.
     */
    public function assignedByDoctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by_doctor_id');
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
     * Get the doctor_id through patient profile or assigned doctor.
     */
    public function getDoctorId()
    {
        // First try through assigned doctor
        if ($this->assigned_by_doctor_id) {
            return $this->assigned_by_doctor_id;
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
     * Complete the assessment with responses.
     */
    public function complete(array $responses): void
    {
        $scale = $this->scale;
        if (!$scale) {
            throw new \Exception('Scale not found for assessment');
        }

        $totalScore = $scale->calculateScore($responses);
        $interpretation = $scale->interpretScore($totalScore);

        $this->update([
            'responses' => $responses,
            'total_score' => $totalScore,
            'interpretation' => $interpretation,
            'status' => 'completed',
            'completed_at' => now(),
        ]);
    }

    /**
     * Get interpretation text.
     */
    public function getInterpretation(): string
    {
        return $this->interpretation ?? 'No interpretation available.';
    }
}
