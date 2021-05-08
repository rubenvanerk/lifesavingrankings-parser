<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class CompetitionConfig extends Model implements HasMedia
{
    protected $connection = 'default';
    protected $table = 'competition_configs';

    use InteractsWithMedia;

    protected $casts = [
        'parser_config' => 'array',
    ];

    protected $hidden = [
        'parser_config',
    ];

    public function competition()
    {
        return $this->belongsTo(Competition::class);
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
