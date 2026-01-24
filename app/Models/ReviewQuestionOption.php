<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReviewQuestionOption extends Model
{
    protected $fillable = [
        'question_id',
        'option_text',
        'option_value',
        'order',
    ];

    protected $casts = [
        'order' => 'integer',
    ];

    /**
     * Get the question this option belongs to
     */
    public function question(): BelongsTo
    {
        return $this->belongsTo(ReviewQuestion::class, 'question_id');
    }
}
