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
        'country_id',
        'start_date',
        'end_date',
        'timekeeping',
    ];

    protected $hidden = [
        'parser_config',
    ];

    public function saveCompetition(): Competition
    {
        $competition = new Competition();
        $competition->name = $this->name;
        $competition->slug = Str::slug($this->name);
        $competition->date = $this->start_date;
        $competition->end_date = $this->end_date;
        $competition->city = $this->city;
        $competition->country_id = $this->country_id;
        $competition->type_of_timekeeping = $this->getTypeOfTimekeepingInt();
        $competition->is_concept = true;
        $competition->status = 2; // = imported
        $competition->file_name = $this->getFirstMediaPath('results_file');
        $competition->save();
        return $competition;
    }

    private function getTypeOfTimekeepingInt(): int
    {
        switch ($this->timekeeping) {
            case 'electronic':
                return 1;
            case 'by_hand':
                return 2;
        }
        return 0; // unknown
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function getFileType(): ?string
    {
        $file = $this->getFirstMediaPath('results_file');

        if (!$file) {
            return null;
        }

        return strtolower(pathinfo($file, PATHINFO_EXTENSION));
    }
}
