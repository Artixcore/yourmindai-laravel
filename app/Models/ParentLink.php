<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class ParentLink extends Model
{
    protected $collection = 'parent_links';

    protected $fillable = [
        'parent_id',
        'patient_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    // Relationships
    public function parent()
    {
        return $this->belongsTo(User::class, 'parent_id');
    }

    public function patient()
    {
        return $this->belongsTo(PatientProfile::class, 'patient_id');
    }
}
