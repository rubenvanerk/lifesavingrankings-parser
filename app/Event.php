<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    public static function getName(int $eventId): string
    {
        if (!isset(self::$allEvents)) {
            $allEvents = Event::all();
            self::$allEvents = $allEvents->keyBy('id')->all();
        }

        return self::$allEvents[$eventId]->name;
    }

    public function results(): HasMany
    {
        return $this->hasMany('App\IndividualResult');
    }
}
