<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Athlete extends Model
{
    public function result()
    {
        return $this->morphOne('App\Result', 'time_setter');
    }
}
