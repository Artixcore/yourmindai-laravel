<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PatientPointAdjustment extends Model
{
    protected $table = 'patient_point_adjustments';

    protected $fillable = [
        'user_id',
        'points_delta',
        'reason',
        'category',
        'created_by_doctor_id',
    ];

    protected $casts = [
        'points_delta' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public const CATEGORIES = ['contingency', 'task', 'other'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function createdByDoctor()
    {
        return $this->belongsTo(User::class, 'created_by_doctor_id');
    }
}
