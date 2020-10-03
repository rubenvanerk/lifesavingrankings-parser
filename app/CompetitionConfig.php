<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class CompetitionConfig extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $casts = [
        'parser_config' => 'array',
    ];

    protected $fillable = [
        'name',
        'city',
        'country',
        'start_date',
        'end_date',
        'timekeeping',
    ];

    protected $hidden = [
        'parser_config',
    ];
}
