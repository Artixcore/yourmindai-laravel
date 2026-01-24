<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PracticeProgression extends Model
{
    protected $fillable = [
        'progressionable_type',
        'progressionable_id',
        'patient_id',
        'progress_date',
        'progress_percentage',
        'status',
        'notes',
        'monitored_by',
        'monitored_by_user_id',
        'metrics',
    ];

    protected $casts = [
        'progress_date' => 'date',
        'metrics' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the parent progressionable model (polymorphic).
     */
    public function progressionable()
    {
        return $this->morphTo();
    }

    /**
     * Get the patient this progression is for.
     */
    public function patient()
    {
        return $this->belongsTo(PatientProfile::class, 'patient_id');
    }

    /**
     * Get the user who monitored this progression.
     */
    public function monitoredByUser()
    {
        return $this->belongsTo(User::class, 'monitored_by_user_id');
    }
}
