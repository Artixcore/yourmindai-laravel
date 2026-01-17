<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PatientPoints extends Model
{
    protected $table = 'patient_points';

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
