<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SleepHygieneLog extends Model
{
    protected $fillable = [
        'patient_profile_id',
        'patient_id',
        'sleep_hygiene_item_id',
        'log_date',
        'is_completed',
        'notes',
    ];

    protected $casts = [
        'log_date' => 'date',
        'is_completed' => 'boolean',
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(SleepHygieneItem::class, 'sleep_hygiene_item_id');
    }

    public function patientProfile(): BelongsTo
    {
        return $this->belongsTo(PatientProfile::class, 'patient_profile_id');
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }
}
