<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Storage;

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

    protected static function booted()
    {
        // Move old results file to Media library structure
        static::retrieved(function (CompetitionConfig $competitionConfig) {
            $file = $competitionConfig->getFirstMediaPath('results_file');
            if (!$file && $competitionConfig->competition->file_name) {
                $currentFileName = $competitionConfig->competition->file_name;

                $competitionConfig->addMediaFromDisk($currentFileName, config('media-library.disk_name'))
                    ->setName($currentFileName)
                    ->toMediaCollection('results_file');
                Storage::disk(config('media-library.disk_name'))->delete($currentFileName);
                $competitionConfig->refresh();

                // Update competition with new path
                $competitionConfig->competition->file_name = $competitionConfig->getFirstMediaPath('results_file');
                $competitionConfig->competition->save();
            }
        });
    }

    public function competition(): BelongsTo
    {
        return $this->belongsTo(Competition::class);
    }

    public function country(): BelongsTo
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
