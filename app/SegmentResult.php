<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SegmentResult extends Model
{
    protected $connection = 'rankings';
    protected $table = 'rankings_segmentresult';
    protected $guarded = [];

    public function relayResult(): BelongsToMany
    {
        return $this->belongsToMany(RelayResult::class);
    }

    public function athlete(): BelongsTo
    {
        return $this->belongsTo(Athlete::class, 'athlete_id');
    }
}
