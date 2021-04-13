<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RelayResult extends Model
{
    protected $connection = 'rankings';
    protected $table = 'rankings_relayresult';
    protected $guarded = [];

    public function competition(): BelongsTo
    {
        return $this->belongsTo(Competition::class, 'competition_id');
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    public function relayTeam(): BelongsTo
    {
        return $this->belongsTo(RelayTeam::class);
    }

    public function segmentResults(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(SegmentResult::class);
    }
}
