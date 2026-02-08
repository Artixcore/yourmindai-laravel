<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppContext extends Model
{
    protected $table = 'app_contexts';

    protected $fillable = ['name'];

    public function feedback()
    {
        return $this->morphMany(Feedback::class, 'feedbackable');
    }
}
