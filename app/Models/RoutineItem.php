<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoutineItem extends Model
{
    protected $fillable = [
        'routine_id',
        'order',
        'title',
        'description',
        'time_of_day',
        'scheduled_time',
        'estimated_minutes',
        'is_required',
    ];

    protected $casts = [
        'order' => 'integer',
        'scheduled_time' => 'time',
        'estimated_minutes' => 'integer',
        'is_required' => 'boolean',
    ];

    public function routine()
    {
        return $this->belongsTo(Routine::class, 'routine_id');
    }

    public function logs()
    {
        return $this->hasMany(RoutineLog::class, 'routine_item_id');
    }
}
