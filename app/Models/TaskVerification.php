<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskVerification extends Model
{
    protected $table = 'task_verifications';

    protected $fillable = [
        'task_id',
        'parent_user_id',
        'verifier_role',
        'verified_at',
        'remarks',
    ];

    protected $casts = [
        'verified_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function parentUser()
    {
        return $this->belongsTo(User::class, 'parent_user_id');
    }
}
