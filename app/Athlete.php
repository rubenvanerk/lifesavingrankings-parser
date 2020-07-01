<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Athlete extends Model
{
    protected $guarded = [];

    public function result(): MorphOne
    {
        return $this->morphOne('App\Result', 'time_setter');
    }

    public function teams(): HasManyThrough
    {
        return $this->hasManyThrough('App\Teams', 'App\Participation');
    }
}
