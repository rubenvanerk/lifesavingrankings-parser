<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IndividualResult extends Model
{
    protected $connection = 'rankings';
    protected $table = 'rankings_individualresult';
    public $timestamps = false;

    public function splits(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(IndividualResultSplit::class, 'individual_result_id');
    }

    public function athlete(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Athlete::class, 'athlete_id');
    }

    public function competition(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Competition::class, 'competition_id');
    }

    public function event(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Event::class, 'event_id');
    }
}
