<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class Event extends Model
{
    protected $connection = 'rankings';
    protected $table = 'rankings_event';
    public $timestamps = false;

    public const EVENT_TYPE_INDIVIDUAL = 1;
    public const EVENT_TYPE_RELAY_SEGMENT = 2;
    public const EVENT_TYPE_RELAY = 3;

    protected static array $allEvents;

    public static function get(int $eventId): Event
    {
        if (!isset(self::$allEvents)) {
            $allEvents = Event::all();
            self::$allEvents = $allEvents->keyBy('id')->all();
        }

        return self::$allEvents[$eventId];
    }

    public function results(): HasMany
    {
        $this->hasMany('App\IndividualResult');
    }
}
