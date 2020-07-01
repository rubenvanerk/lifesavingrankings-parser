<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Team extends Model
{
    protected $fillable = ['name'];

    public function athletes(): HasManyThrough
    {
        return $this->hasManyThrough('App\Athlete', 'App\Participation');
    }
}
