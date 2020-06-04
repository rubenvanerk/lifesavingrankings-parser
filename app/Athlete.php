<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Athlete extends Model
{
    protected $table = 'rankings_athlete';
    public $timestamps = false;

    public function nationalities()
    {
        return $this->belongsToMany('CompetitionParser\Classes\Models\Nationality', 'rankings_athlete_nationalities');
    }
}
