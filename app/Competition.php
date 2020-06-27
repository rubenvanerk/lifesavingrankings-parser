<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Competition extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = [
        'name',
        'city',
        'country',
        'start_date',
        'end_date',
        'timekeeping',
    ];

    public function results() {
        $this->hasMany('App\Result');
    }
}
