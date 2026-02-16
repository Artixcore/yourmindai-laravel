<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BehaviorContingencyPlanItem extends Model
{
    protected $table = 'behavior_contingency_plan_items';

    protected $fillable = [
        'plan_id',
        'sort_order',
        'target_behavior',
        'condition_stimulus',
        'reward_if_followed',
        'punishment_if_not_followed',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function plan(): BelongsTo
    {
        return $this->belongsTo(BehaviorContingencyPlan::class, 'plan_id');
    }

    public function checkins(): HasMany
    {
        return $this->hasMany(BehaviorContingencyCheckin::class, 'plan_item_id');
    }
}
