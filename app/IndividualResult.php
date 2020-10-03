<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\IndividualResult
 *
 * @property int $id
 * @property string|null $time
 * @property int $athlete_id
 * @property int $competition_id
 * @property int $event_id
 * @property int|null $extra_analysis_time_by_id
 * @property int $points
 * @property string|null $original_line
 * @property int $round
 * @property bool $disqualified
 * @property bool $did_not_start
 * @property bool $withdrawn
 * @property int|null $heat
 * @property int|null $lane
 * @property string|null $reaction_time
 * @property-read \App\Athlete $athlete
 * @property-read \App\CompetitionConfig $competition
 * @property-read \App\Event $event
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\IndividualResultSplit[] $splits
 * @property-read int|null $splits_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\IndividualResult newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\IndividualResult newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\IndividualResult query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\IndividualResult whereAthleteId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\IndividualResult whereCompetitionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\IndividualResult whereDidNotStart($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\IndividualResult whereDisqualified($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\IndividualResult whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\IndividualResult whereExtraAnalysisTimeById($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\IndividualResult whereHeat($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\IndividualResult whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\IndividualResult whereLane($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\IndividualResult whereOriginalLine($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\IndividualResult wherePoints($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\IndividualResult whereReactionTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\IndividualResult whereRound($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\IndividualResult whereTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\IndividualResult whereWithdrawn($value)
 */
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
