<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HomeworkMedia extends Model
{
    protected $table = 'homework_media';

    protected $fillable = [
        'homework_assignment_id',
        'type',
        'url',
        'title',
    ];

    public const TYPES = ['video', 'audio', 'podcast', 'link'];

    public function homeworkAssignment(): BelongsTo
    {
        return $this->belongsTo(HomeworkAssignment::class, 'homework_assignment_id');
    }
}
