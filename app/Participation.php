<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Participation extends Model
{
    protected $table = 'rankings_participation';
    protected $guarded = [];
    public $timestamps = false;

    public function competition(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Competition::class, 'competition_id');
    }

    public function athlete(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Athlete::class, 'athlete_id');
    }

    public function team(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Team::class, 'team_id');
    }
}
