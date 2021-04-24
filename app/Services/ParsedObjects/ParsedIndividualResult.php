<?php

namespace App\Services\ParsedObjects;

use App\Event;
use App\IndividualResult;
use Carbon\CarbonInterval;
use ParseError;

class ParsedIndividualResult extends ParsedResult
{
    public function __construct(
        ?CarbonInterval $time,
        ParsedAthlete $athlete,
        int $round,
        bool $disqualified,
        bool $didNotStart,
        bool $withdrawn,
        ?string $originalLine,
        ?int $heat,
        ?int $lane,
        ?CarbonInterval $reactionTime,
        ?array $splits
    ) {
        if (!$disqualified && !$didNotStart && !$withdrawn && is_null($time)) {
            throw new ParseError('Time can not be null if DSQ, DNS and WDR are false');
        }
        if ((int)$disqualified + (int)$didNotStart + (int)$withdrawn > 1) {
            throw new ParseError(
                sprintf(
                    'Only one of DSQ, DNS or WDR can be true. Given values: DSQ: %b, DNS: %b, WDR: %b',
                    $disqualified,
                    $didNotStart,
                    $withdrawn
                )
            );
        }
        $this->time = $time;
        $this->athlete = $athlete;
        $this->round = $round;
        $this->disqualified = $disqualified;
        $this->didNotStart = $didNotStart;
        $this->withdrawn = $withdrawn;
        $this->originalLine = $originalLine;
        $this->heat = $heat;
        $this->splits = $splits;
        $this->lane = $lane;
        $this->reactionTime = $reactionTime;
    }

    public function saveToDatabase(): void
    {
        $athlete = $this->athlete->saveToDatabase();

        $event = Event::exists($this->eventId);

        $result = new IndividualResult();
        $result->athlete()->associate($athlete);
        $result->event()->associate($event);
        $result->competition()->associate(ParsedCompetition::$competition);
        $result->time = $this->time ? $this->time->totalMilliseconds / 1000 : null;
        $result->points = $this->calculatePoints();
        $result->original_line = $this->originalLine;
        $result->round = $this->round;
        $result->disqualified = $this->disqualified;
        $result->did_not_start = $this->didNotStart;
        $result->withdrawn = $this->withdrawn;
        $result->lane = $this->lane;
        $result->heat = $this->heat;
        $result->save();
    }
}
