<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EventRecord extends Model
{
    protected $connection = 'rankings';
    protected $table = 'rankings_eventrecord';
    public $timestamps = false;


    public function event(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Event::class, 'event_id');
    }
}
