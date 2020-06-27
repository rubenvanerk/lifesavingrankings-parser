<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    public function results()
    {
        $this->hasMany('App\Result');
    }
}
