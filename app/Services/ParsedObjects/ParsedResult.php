<?php

namespace App\Services\ParsedObjects;

use App\Event;
use App\EventRecord;
use App\IndividualResult;
use App\Services\Cleaners\Cleaner;
use Carbon\CarbonInterval;

class ParsedResult implements ParsedObject
{
    public $time;
    public $athlete;
    public $round;
    public $disqualified;
    public $didNotStart;
    public $withdrawn;
    public $originalLine;
    public $heat;
    public $splits;
    public $lane;
    public $reactionTime;
    public $eventId;

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
            throw new \ParseError('Time can not be null if DSQ, DNS and WDR are false');
        }
        if ($disqualified + $didNotStart + $withdrawn > 1) {
            throw new \ParseError(
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

    public function getStatus(): ?string
    {
        if ($this->disqualified) {
            return 'DSQ';
        }
        if ($this->didNotStart) {
            return 'DNS';
        }
        if ($this->withdrawn) {
            return 'WDR';
        }
        return null;
    }

    public function getTimeStringForDisplay()
    {
        if (!$this->time) {
            return $this->getStatus();
        }
        return str_replace('0000', '', $this->time->format('%I:%S.%F'));
    }

    public function getReactionTimeStringForDisplay()
    {
        if (is_null($this->reactionTime)) {
            return '';
        }
        return str_replace('0000', '', $this->reactionTime->format('%S.%F'));
    }

    public function calculatePoints(): ?float
    {
        if ($this->disqualified || $this->didNotStart || $this->withdrawn) {
            return 0;
        }

        $eventId = $this->eventId;
        $record = EventRecord::whereHas('event', function ($query) use ($eventId) {
            return $query->where('id', $eventId)->where('gender', $this->athlete->gender);
        })->first();

        if (!$record) {
            return 0;
        }

        $recordTime = Cleaner::cleanTime($record->time);

        $points = 0;
        $quotient = $this->time->totalSeconds / $recordTime->totalSeconds;

        if ($quotient <= 2) {
            $r1 = 467 * $quotient * $quotient;
            $r2 = 2001 * $quotient;
            $points = round(($r1 - $r2 + 2534.0) * 100.0) / 100;
        } elseif ($quotient <= 5) {
            $r1 = 2000.0 / 3.0;
            $r2 = (400.0 / 3.0) * $quotient;
            $points = $r1 - $r2;
            $points = round(100.0 * $points) / 100.0;
        }

        return $points;
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
