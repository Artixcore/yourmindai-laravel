<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WellbeingLog extends Model
{
    protected $table = 'wellbeing_logs';

    protected $fillable = [
        'patient_profile_id',
        'log_date',
        'screentime_minutes',
        'details',
        'lifestyle_errors',
    ];

    protected $casts = [
        'log_date' => 'date',
        'details' => 'array',
        'lifestyle_errors' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function patientProfile(): BelongsTo
    {
        return $this->belongsTo(PatientProfile::class, 'patient_profile_id');
    }
}
