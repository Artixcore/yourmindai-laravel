<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BehaviorContingencyCheckin extends Model
{
    protected $table = 'behavior_contingency_checkins';

    protected $fillable = [
        'plan_id',
        'plan_item_id',
        'date',
        'followed',
        'client_note',
        'reviewer_id',
        'reviewer_note',
        'applied_reward',
        'applied_punishment',
    ];

    protected $casts = [
        'date' => 'date',
        'followed' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the plan this check-in belongs to.
     */
    public function plan(): BelongsTo
    {
        return $this->belongsTo(BehaviorContingencyPlan::class, 'plan_id');
    }

    /**
     * Get the plan item this check-in belongs to.
     */
    public function planItem(): BelongsTo
    {
        return $this->belongsTo(BehaviorContingencyPlanItem::class, 'plan_item_id');
    }

    /**
     * Get the reviewer (doctor/admin).
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }
}
