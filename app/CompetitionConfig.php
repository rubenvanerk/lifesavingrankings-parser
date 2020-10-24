<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Str;

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
        $competition->slug = Str::slug($this->name);
        $competition->date = $this->start_date; // TODO: make lsr support start-end date
        $competition->location = $this->city . ', ' .$this->country; // TODO: make lsr support city / country
        $competition->type_of_timekeeping = $this->getTypeOfTimekeepingInt();
        $competition->is_concept = true;
        $competition->status = 2; // imported
        $competition->save();
        return $competition;
    }

    private function getTypeOfTimekeepingInt()
    {
        switch ($this->timekeeping) {
            case 'electronic':
                return 1;
            case 'by_hand':
                return 2;
        }
        return 0; // unknown
    }
}
