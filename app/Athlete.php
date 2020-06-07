<?php

namespace App;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Model;

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
