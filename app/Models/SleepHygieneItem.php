<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SleepHygieneItem extends Model
{
    protected $fillable = ['label', 'sort_order', 'is_default'];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    public function logs(): HasMany
    {
        return $this->hasMany(SleepHygieneLog::class, 'sleep_hygiene_item_id');
    }
}
