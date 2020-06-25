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
    )
    {
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

        $event = Event::findOrFail($this->eventId);

        $individualResult = new IndividualResult();
        $individualResult->athlete()->associate($athlete);
        $individualResult->event()->associate($event);
        $individualResult->competition()->associate(ParsedCompetition::$model);
        $individualResult->time = $this->time ? sprintf('%s:%s.%s', $this->time->minutes, $this->time->seconds, $this->time->microseconds) : null;
        $individualResult->points = $this->calculatePoints();
        $individualResult->original_line = $this->originalLine;
        $individualResult->round = $this->round;
        $individualResult->disqualified = $this->disqualified;
        $individualResult->did_not_start = $this->didNotStart;
        $individualResult->withdrawn = $this->withdrawn;
        $individualResult->lane = $this->lane;
        $individualResult->heat = $this->heat;
        $individualResult->save();
    }
}
