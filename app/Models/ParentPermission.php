<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ParentPermission extends Model
{
    protected $fillable = [
        'parent_id',
        'patient_id',
        'can_view_medical_records',
        'can_view_session_notes',
        'can_provide_feedback',
        'can_view_progress',
        'can_view_assessments',
        'can_communicate_with_doctor',
        'notes',
    ];

    protected $casts = [
        'can_view_medical_records' => 'boolean',
        'can_view_session_notes' => 'boolean',
        'can_provide_feedback' => 'boolean',
        'can_view_progress' => 'boolean',
        'can_view_assessments' => 'boolean',
        'can_communicate_with_doctor' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the parent user.
     */
    public function parent()
    {
        return $this->belongsTo(User::class, 'parent_id');
    }

    /**
     * Get the patient.
     */
    public function patient()
    {
        return $this->belongsTo(PatientProfile::class, 'patient_id');
    }
}
