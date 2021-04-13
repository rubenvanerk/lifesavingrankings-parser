<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventRecord extends Model
{
    protected $connection = 'rankings';
    protected $table = 'rankings_eventrecord';
    public $timestamps = false;


    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class, 'event_id');
    }
}
