<?php

namespace App\Services\ParsedObjects;

use App\Competition;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class ParsedCompetition implements ParsedObject {
    private $id;
    public $name;
    public $location;
    public $date;
    public $timekeeping;
    public $credit;
    public $resultCount = 0;
    public $events = [];

    private CONST STATUS_IMPORTED = 2;

    public function __construct(string $name, string $location, string $date, int $timekeeping, string $credit)
    {
        $this->name = $name;
        $this->location = $location;
        $this->date = $date;
        $this->timekeeping = $timekeeping;
        $this->credit = $credit;
    }

    public function addEvent(ParsedEvent $event): void
    {
        $lastEvent = Arr::last($this->events);
        if ($lastEvent && $lastEvent->id === $event->id) {
            return;
        }
        $this->events[] = $event;
    }

    public function addResultToLastEvent(ParsedResult $result): void
    {
        Arr::last($this->events)->addResult($result);
    }

    public function addResultToEvent(ParsedResult $result, int $eventId): void
    {
        $parsedEvent = $this->findEventById($eventId);
        if (is_null($parsedEvent)) {
            $parsedEvent = new ParsedEvent($eventId);
            $this->addEvent($parsedEvent);
        }
        $parsedEvent->addResult($result);
    }

    private function findEventById(int $eventId): ?ParsedEvent
    {
        $filtered = Arr::where($this->events, function ($value, $key) use ($eventId) {
            return $value->id === $eventId;
        });
        return Arr::first($filtered);
    }

    public function saveToDatabase()
    {
        $competitionSlug = Str::slug($this->name);
        $competition = Competition::firstOrCreate(
            ['slug' => $competitionSlug],
            [
                'name' => $this->name,
                'date' => $this->date,
                'location' => $this->location,
                'type_of_timekeeping' => $this->timekeeping,
                'is_concept' =>  true,
                'file_name' => 'filename', // TODO
                'credit' => $this->credit ?: null,
                'status' => self::STATUS_IMPORTED
            ]
        );
        $this->id = $competition->id;

    }
}
