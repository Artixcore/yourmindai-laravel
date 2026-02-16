<?php

namespace App\Models;

use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;

class Patient extends Model implements AuthenticatableContract
{
    use Authenticatable, HasApiTokens;
    protected $fillable = [
        'doctor_id',
        'name',
        'email',
        'phone',
        'password',
        'photo_path',
        'status',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'status' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the doctor that owns the patient.
     */
    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    /**
     * Get the sessions for the patient.
     */
    public function sessions()
    {
        return $this->hasMany(Session::class, 'patient_id');
    }

    /**
     * Get the resources for the patient.
     */
    public function resources()
    {
        return $this->hasMany(PatientResource::class, 'patient_id');
    }

    /**
     * Get the messages for the patient.
     */
    public function messages()
    {
        return $this->hasMany(PatientMessage::class, 'patient_id');
    }

    /**
     * Get the medications for the patient.
     */
    public function medications()
    {
        return $this->hasMany(PatientMedication::class, 'patient_id');
    }

    /**
     * Get the journal entries for the patient.
     */
    public function journalEntries()
    {
        return $this->hasMany(PatientJournalEntry::class, 'patient_id');
    }

    /**
     * Get the devices for the patient.
     */
    public function devices()
    {
        return $this->hasMany(PatientDevice::class, 'patient_id');
    }

    /**
     * Get the psychometric assessments for the patient.
     */
    public function psychometricAssessments()
    {
        return $this->hasMany(PsychometricAssessment::class, 'patient_id');
    }

    /**
     * Get the contingency plans for the patient.
     */
    public function contingencyPlans()
    {
        return $this->hasMany(ContingencyPlan::class, 'patient_id');
    }

    /**
     * Get the contingency activations for the patient.
     */
    public function contingencyActivations()
    {
        return $this->hasMany(ContingencyActivation::class, 'patient_id');
    }

    /**
     * Get the behavior contingency plans for the patient.
     */
    public function behaviorContingencyPlans()
    {
        return $this->hasMany(BehaviorContingencyPlan::class, 'patient_id');
    }

    /**
     * Get the reviews written by the patient.
     */
    public function reviews()
    {
        return $this->hasMany(Review::class, 'patient_id');
    }

    /**
     * Resolve Patient to PatientProfile for models that use patient_profile_id.
     * Web-created patients have both Patient and PatientProfile linked via User (same email).
     */
    public function resolvePatientProfile(): ?PatientProfile
    {
        $user = User::where('email', $this->email)->first();
        if (!$user) {
            return null;
        }
        return PatientProfile::where('user_id', $user->id)
            ->where('doctor_id', $this->doctor_id)
            ->first();
    }

    /**
     * Get the photo URL attribute.
     */
    public function getPhotoUrlAttribute()
    {
        if ($this->photo_path) {
            return asset('storage/' . $this->photo_path);
        }
        return null;
    }

    /**
     * Automatically hash password when setting it.
     */
    public function setPasswordAttribute($value)
    {
        // Only hash if the value is not already a bcrypt hash
        if (!empty($value)) {
            // Check if it's already a bcrypt hash (starts with $2y$)
            if (strlen($value) === 60 && strpos($value, '$2y$') === 0) {
                // Already hashed, use as is
                $this->attributes['password'] = $value;
            } else {
                // Not hashed, hash it
                $this->attributes['password'] = Hash::make($value);
            }
        }
    }
}
