<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PatientJournalEntry extends Model
{
    protected $fillable = [
        'patient_id',
        'mood_score',
        'notes',
        'entry_date',
        'tags',
    ];

    protected $casts = [
        'mood_score' => 'integer',
        'entry_date' => 'date',
        'tags' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the patient that owns the journal entry.
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }
}
