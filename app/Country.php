<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Country extends Model
{
    protected $connection = 'rankings';
    protected $table = 'rankings_country';
    public $timestamps = false;

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope('order', function (Builder $builder) {
            $builder->orderBy('name');
        });
    }

    public function athletes(): BelongsToMany
    {
        return $this->belongsToMany(Athlete::class);
    }

    public function competitions(): BelongsToMany
    {
        return $this->belongsToMany(Competition::class);
    }
}
