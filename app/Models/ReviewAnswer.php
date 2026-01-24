<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReviewAnswer extends Model
{
    protected $fillable = [
        'review_id',
        'question_id',
        'answer_value',
        'answer_text',
    ];

    /**
     * Get the review this answer belongs to
     */
    public function review(): BelongsTo
    {
        return $this->belongsTo(Review::class);
    }

    /**
     * Get the question this answer is for
     */
    public function question(): BelongsTo
    {
        return $this->belongsTo(ReviewQuestion::class, 'question_id');
    }

    /**
     * Get formatted answer display
     */
    public function getFormattedAnswerAttribute(): string
    {
        $question = $this->question;

        switch ($question->question_type) {
            case 'star_rating':
                return $this->answer_value . ' / 5 stars';
            
            case 'yes_no':
                return $this->answer_value === '1' || $this->answer_value === 'yes' ? 'Yes' : 'No';
            
            case 'multiple_choice':
                // Try to find the option text
                $option = $question->options()->where('option_value', $this->answer_value)->first();
                return $option ? $option->option_text : $this->answer_value;
            
            default:
                return $this->answer_value;
        }
    }

    /**
     * Get star rating as integer (for star_rating questions)
     */
    public function getStarRatingAttribute(): ?int
    {
        if ($this->question->question_type === 'star_rating') {
            return (int) $this->answer_value;
        }
        return null;
    }
}
