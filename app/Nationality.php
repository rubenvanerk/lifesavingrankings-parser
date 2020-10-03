<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Nationality extends Model
{
    protected $connection = 'rankings';
    protected $table = 'rankings_nationality';
    public $timestamps = false;

    public function athletes(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Athlete::class);
    }
}
