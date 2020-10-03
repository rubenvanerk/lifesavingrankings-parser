<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Competition extends Model
{
    protected $connection = 'rankings';
    protected $table = 'rankings_competition';
    public $timestamps = false;

    protected $fillable = [
        'slug',
        'name',
        'date',
        'location',
        'type_of_timekeeping',
        'is_concept',
        'file_name',
        'credit',
        'status',
    ];
}
