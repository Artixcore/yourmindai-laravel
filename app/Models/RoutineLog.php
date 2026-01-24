<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoutineLog extends Model
{
    protected $fillable = [
        'routine_item_id',
        'patient_id',
        'log_date',
        'completed_at',
        'is_completed',
        'is_skipped',
        'notes',
    ];

    protected $casts = [
        'log_date' => 'date',
        'completed_at' => 'time',
        'is_completed' => 'boolean',
        'is_skipped' => 'boolean',
    ];

    public function routineItem()
    {
        return $this->belongsTo(RoutineItem::class, 'routine_item_id');
    }

    public function patient()
    {
        return $this->belongsTo(PatientProfile::class, 'patient_id');
    }
}
