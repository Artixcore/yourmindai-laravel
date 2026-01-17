<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class AssessmentResult extends Model
{
    protected $collection = 'assessment_results';

    protected $fillable = [
        'assessment_id',
        'score',
        'sub_scores',
        'interpretation',
        'responses',
        'substance_type',
        'completed_at',
    ];

    protected $casts = [
        'score' => 'integer',
        'sub_scores' => 'array',
        'responses' => 'array',
        'completed_at' => 'datetime',
    ];

    public function assessment()
    {
        return $this->belongsTo(Assessment::class, 'assessment_id');
    }
}
