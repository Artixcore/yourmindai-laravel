<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasFeedback;

class SessionReport extends Model
{
    use HasFeedback;

    protected $fillable = [
        'session_id',
        'patient_id',
        'created_by',
        'title',
        'summary',
        'assessments_summary',
        'techniques_assigned',
        'progress_notes',
        'next_steps',
        'shared_with',
        'shared_with_patient',
        'shared_with_parents',
        'shared_with_others',
        'status',
        'finalized_at',
        'pdf_path',
    ];

    protected $casts = [
        'shared_with' => 'array',
        'shared_with_patient' => 'boolean',
        'shared_with_parents' => 'boolean',
        'shared_with_others' => 'boolean',
        'finalized_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function session()
    {
        return $this->belongsTo(Session::class, 'session_id');
    }

    public function patient()
    {
        return $this->belongsTo(PatientProfile::class, 'patient_id');
    }

    public function createdByDoctor()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
