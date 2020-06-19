<?php

namespace App;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Athlete
 *
 * @property int $id
 * @property string|null $first_name
 * @property string|null $last_name
 * @property int|null $year_of_birth
 * @property int $gender
 * @property string|null $slug
 * @property string $name
 * @property int|null $alias_of_id
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property-read \App\Athlete|null $alias_of
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Nationality[] $nationalities
 * @property-read int|null $nationalities_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Athlete findSimilarSlugs($attribute, $config, $slug)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Athlete newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Athlete newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Athlete query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Athlete whereAliasOfId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Athlete whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Athlete whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Athlete whereGender($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Athlete whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Athlete whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Athlete whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Athlete whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Athlete whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Athlete whereYearOfBirth($value)
 */
class Athlete extends Model
{
    use Sluggable;

    protected $table = 'rankings_athlete';
    public $timestamps = false;

    public function nationalities(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Nationality::class, 'rankings_athlete_nationalities');
    }

    public function alias_of(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(self::class, 'alias_of_id');
    }

    protected $fillable = ['name', 'gender', 'year_of_birth'];

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name'
            ]
        ];
    }
}
