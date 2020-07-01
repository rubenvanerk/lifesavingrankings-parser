<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Participation extends Model
{
    public function team(): BelongsTo
    {
        return $this->belongsTo('App\Team');
    }

    public function athlete(): BelongsTo
    {
        return $this->belongsTo('App\Athlete');
    }

    public function competition(): BelongsTo
    {
        return $this->belongsTo('App\Competition');
    }
}
