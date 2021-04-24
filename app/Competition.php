<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Competition extends Model
{
    protected $connection = 'rankings';
    protected $table = 'rankings_competition';
    public $timestamps = false;

    protected $fillable = [
        'slug',
        'name',
        'original_name',
        'date',
        'location',
        'type_of_timekeeping',
        'is_concept',
        'file_name',
        'credit',
        'status',
        'comment',
    ];

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }
}
