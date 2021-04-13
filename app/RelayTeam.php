<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RelayTeam extends Model
{
    protected $connection = 'rankings';
    protected $table = 'rankings_relayteam';
    protected $guarded = [];

    public function relayResults(): HasMany
    {
        return $this->hasMany(RelayResult::class);
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'team_id');
    }
}
