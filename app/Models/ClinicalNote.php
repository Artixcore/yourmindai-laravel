<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClinicalNote extends Model
{
    protected $table = 'clinical_notes';

    protected $fillable = [
        'patient_id',
        'author_id',
        'raw_text',
        'ai_summary',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    // Relationships
    public function patient()
    {
        return $this->belongsTo(PatientProfile::class, 'patient_id');
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }
}
