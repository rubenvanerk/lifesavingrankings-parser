<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    public const EVENT_TYPE_INDIVIDUAL = 1;
    public const EVENT_TYPE_RELAY_SEGMENT = 2;
    public const EVENT_TYPE_RELAY = 3;

    public function results()
    {
        $this->hasMany('App\Result');
    }
}
