<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class IndividualResult extends Model
{
    protected $connection = 'rankings';
    protected $table = 'rankings_individualresult';
    public $timestamps = false;

    protected $fillable = [
        'athlete_id',
        'event_id',
        'competition_id',
        'time',
        'points',
        'original_line',
        'round',
        'disqualified',
        'did_not_start',
        'withdrawn',
        'lane',
        'heat',
    ];

    public function splits(): HasMany
    {
        return $this->hasMany(IndividualResultSplit::class, 'individual_result_id');
    }

    public function athlete(): BelongsTo
    {
        return $this->belongsTo(Athlete::class, 'athlete_id');
    }

    public function competition(): BelongsTo
    {
        return $this->belongsTo(Competition::class, 'competition_id');
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class, 'event_id');
    }
}
