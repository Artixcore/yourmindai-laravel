<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Reminder extends Model
{
    protected $collection = 'reminders';

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'scheduled_time',
        'is_active',
    ];

    protected $casts = [
        'scheduled_time' => 'datetime',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
