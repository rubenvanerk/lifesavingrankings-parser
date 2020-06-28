<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Athlete extends Model
{
    protected $guarded = [];

    public function result()
    {
        return $this->morphOne('App\Result', 'time_setter');
    }
}
