<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Participation
 *
 * @property int $id
 * @property int $athlete_id
 * @property int $competition_id
 * @property int $team_id
 * @property-read \App\Athlete $athlete
 * @property-read \App\Competition $competition
 * @property-read \App\Team $team
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Participation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Participation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Participation query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Participation whereAthleteId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ParticipatiCon whereCompetitionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Participation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Participation whereTeamId($value)
 */
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
