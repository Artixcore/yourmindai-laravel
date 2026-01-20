<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientIntake extends Model
{
    protected $table = 'client_intakes';

    protected $fillable = [
        'user_id',
        'responses',
        'summary',
    ];

    protected $casts = [
        'responses' => 'array',
        'summary' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
