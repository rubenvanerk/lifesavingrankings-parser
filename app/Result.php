<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Result extends Model
{
    public function event()
    {
        return $this->belongsTo('App\Event');
    }

    public function time_setter()
    {
        return $this->morphTo();
    }

    public function competition()
    {
        return $this->belongsTo('App\Competition');
    }
}
