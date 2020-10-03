<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Nationality
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $flag_code
 * @property int|null $parent_id
 * @property bool $is_parent_country
 * @property string|null $lenex_code
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Athlete[] $athletes
 * @property-read int|null $athletes_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Nationality newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Nationality newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Nationality query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Nationality whereFlagCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Nationality whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Nationality whereIsParentCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Nationality whereLenexCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Nationality whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Nationality whereParentId($value)
 */
class Nationality extends Model
{
    protected $connection = 'rankings';
    protected $table = 'rankings_nationality';
    public $timestamps = false;

    public function athletes(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Athlete::class);
    }
}
