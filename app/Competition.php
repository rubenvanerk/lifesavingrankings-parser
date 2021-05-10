<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Str;

class Competition extends Model
{
    use HasSlug;

    protected $connection = 'rankings';
    protected $table = 'rankings_competition';
    public $timestamps = false;

    protected $fillable = [
        'slug',
        'name',
        'original_name',
        'date',
        'end_date',
        'city',
        'country_id',
        'type_of_timekeeping',
        'is_concept',
        'file_name',
        'credit',
        'status',
        'comment',
    ];

    protected static function booted()
    {
        static::addGlobalScope('not_added_by_user', function (Builder $builder) {
            $builder->where('slug', '!=', '');
        });

        static::retrieved(function ($competition) {
            if (!$competition->competition_config()->exists()) {
                $competition->competition_config()->create();
            }
        });

        self::creating(function ($model) {
            $model->slug = Str::slug($model->name);
            $model->is_concept = true;
            $model->status = 1; // scheduled for import
        });

        static::created(function ($competition) {
            if (!$competition->competition_config()->exists()) {
                $competition->competition_config()->create();
            }
        });
    }

    /**
     * Get the options for generating the slug.
     */
    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    public function competition_config(): HasOne
    {
        return $this->hasOne(CompetitionConfig::class);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function individual_results(): HasMany
    {
        return $this->hasMany(IndividualResult::class);
    }
}
