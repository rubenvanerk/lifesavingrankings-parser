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

    public function saveCompetition()
    {
        $competition = new Competition();
        $competition->name = $this->name;
        $competition->date = $this->start_date; // TODO: make lsr support start-end date
        $competition->location = $this->city . ', ' .$this->country; // TODO: make lsr support city / country
        $competition->save();
        return $competition;
    }
}
