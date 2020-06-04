<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Nationality extends Model
{
    protected $table = 'rankings_nationality';
    public $timestamps = false;

    public function athletes()
    {
        return $this->belongsToMany(Athlete::class);
    }
}
