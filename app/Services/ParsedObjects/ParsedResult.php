<?php

namespace App\Services\ParsedObjects;

use App\Event;
use App\EventRecord;
use App\IndividualResult;
use App\Services\Cleaners\Cleaner;
use Carbon\CarbonInterval;

class ParsedResult implements ParsedObject
{
    /** @var CarbonInterval|null  */
    public $time;
    /** @var ParsedAthlete  */
    public $athlete;
    /** @var int  */
    public $round;
    /** @var bool  */
    public $disqualified;
    /** @var bool  */
    public $didNotStart;
    /** @var bool  */
    public $withdrawn;
    /** @var string|null  */
    public $originalLine;
    /** @var int|null  */
    public $heat;
    /** @var array|null  */
    public $splits;
    /** @var int|null  */
    public $lane;
    /** @var CarbonInterval|null  */
    public $reactionTime;
    /** @var int */
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
        if ((int)$disqualified + (int)$didNotStart + (int)$withdrawn > 1) {
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

    public function getTimeStringForDisplay(): string
    {
        if (!$this->time) {
            return $this->getStatus() ?? '';
        }
        return str_replace('0000', '', $this->time->format('%I:%S.%F'));
    }

    public function getReactionTimeStringForDisplay(): string
    {
        if (is_null($this->reactionTime)) {
            return '';
        }
        return str_replace('0000', '', $this->reactionTime->format('%S.%F'));
    }

    public function calculatePoints(): int
    {
        if ($this->disqualified || $this->didNotStart || $this->withdrawn || is_null($this->time)) {
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

        return (int)(1000 * (($recordTime->totalSeconds / $this->time->totalSeconds) ** 3));
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
