<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PatientMedicationLog extends Model
{
    protected $fillable = [
        'medication_id',
        'patient_id',
        'taken_date',
        'taken_time',
        'taken',
        'notes',
    ];

    protected $casts = [
        'taken_date' => 'date',
        'taken_time' => 'datetime',
        'taken' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the medication that this log belongs to.
     */
    public function medication(): BelongsTo
    {
        return $this->belongsTo(PatientMedication::class, 'medication_id');
    }

    /**
     * Get the patient that owns the log.
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }
}
