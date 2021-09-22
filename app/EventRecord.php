<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;

class EventRecord extends Model
{
    protected $connection = 'rankings';
    protected $table = 'rankings_eventrecord';
    public $timestamps = false;

    protected static Collection $allEventRecords;

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class, 'event_id');
    }


    public static function getCached(int $eventId, $gender): EventRecord
    {
        if (!isset(self::$allEventRecords)) {
            self::$allEventRecords = EventRecord::all();
        }

        return self::$allEventRecords->where('gender', $gender)->firstWhere('event_id', $eventId);
    }
}
