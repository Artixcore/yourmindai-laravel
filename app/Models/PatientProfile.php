<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PatientProfile extends Model
{
    protected $table = 'patient_profiles';

    protected $fillable = [
        'patient_number',
        'user_id',
        'doctor_id',
        'full_name',
        'date_of_birth',
        'gender',
        'phone',
        'problem',
        'status',
    ];

    protected $casts = [
        'date_of_birth' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function clinicalNotes()
    {
        return $this->hasMany(ClinicalNote::class, 'patient_id');
    }

    public function goals()
    {
        return $this->hasMany(Goal::class, 'patient_id');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class, 'patient_id');
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'patient_id');
    }

    public function assessments()
    {
        return $this->hasMany(Assessment::class, 'patient_id');
    }

    public function doctorInstructions()
    {
        return $this->hasMany(DoctorInstruction::class, 'patient_id');
    }

    public function sessions()
    {
        return $this->hasMany(Session::class, 'patient_id');
    }

    public function resources()
    {
        return $this->hasMany(PatientResource::class, 'patient_id');
    }

    public function devices()
    {
        return $this->hasMany(PatientDevice::class, 'patient_profile_id');
    }

    public function psychometricAssessments()
    {
        return $this->hasMany(PsychometricAssessment::class, 'patient_profile_id');
    }

    public function contingencyPlans()
    {
        return $this->hasMany(ContingencyPlan::class, 'patient_profile_id');
    }

    public function contingencyActivations()
    {
        return $this->hasMany(ContingencyActivation::class, 'patient_profile_id');
    }

    /**
     * Get the behavior contingency plans for the patient profile.
     */
    public function behaviorContingencyPlans()
    {
        return $this->hasMany(BehaviorContingencyPlan::class, 'patient_profile_id');
    }

    /**
     * Get the parent links (parent-child relationships).
     */
    public function parentLinks()
    {
        return $this->hasMany(ParentLink::class, 'patient_id');
    }
}
