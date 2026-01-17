<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class PatientPoints extends Model
{
    protected $collection = 'patient_points';

    protected $fillable = [
        'user_id',
        'total_points',
    ];

    protected $casts = [
        'total_points' => 'integer',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
