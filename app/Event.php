<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Event
 *
 * @property int $id
 * @property string $name
 * @property int $type
 * @property bool $use_points_in_athlete_total
 * @property string $slug
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Event newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Event newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Event query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Event whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Event whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Event whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Event whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Event whereUsePointsInAthleteTotal($value)
 */
class Event extends Model
{
    protected $table = 'rankings_event';
    public $timestamps = false;

    public const EVENT_TYPE_INDIVIDUAL = 1;
    public const EVENT_TYPE_RELAY_SEGMENT = 2;
    public const EVENT_TYPE_RELAY = 3;
}
