<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Competition
 *
 * @property int $id
 * @property string|null $name
 * @property string $date
 * @property string $location
 * @property int $type_of_timekeeping
 * @property string|null $slug
 * @property bool $is_concept
 * @property int $status
 * @property string|null $file_name
 * @property string|null $published_on
 * @property string|null $credit
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Competition newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Competition newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Competition query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Competition whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Competition whereCredit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Competition whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Competition whereFileName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Competition whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Competition whereIsConcept($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Competition whereLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Competition whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Competition wherePublishedOn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Competition whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Competition whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Competition whereTypeOfTimekeeping($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Competition whereUpdatedAt($value)
 */
class Competition extends Model
{
    protected $table = 'rankings_competition';
    public $timestamps = false;

    protected $fillable = [
        'slug',
        'name',
        'date',
        'location',
        'type_of_timekeeping',
        'is_concept',
        'file_name',
        'credit',
        'status',
    ];
}
