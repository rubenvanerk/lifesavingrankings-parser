<?php

namespace App\Services\ParsedObjects;

use App\EventRecord;
use App\Services\Cleaners\Cleaner;
use Carbon\CarbonInterval;
use Illuminate\Support\Arr;

abstract class ParsedResult implements ParsedObject
{
    /** @var CarbonInterval|null */
    public $time;
    /** @var int */
    public $round;
    /** @var bool */
    public $disqualified;
    /** @var bool */
    public $didNotStart;
    /** @var bool */
    public $withdrawn;
    /** @var string|null */
    public $originalLine;
    /** @var int|null */
    public $heat;
    /** @var array|null */
    public $splits;
    /** @var int|null */
    public $lane;
    /** @var CarbonInterval|null */
    public $reactionTime;
    /** @var int */
    public $eventId;
    /** @var ParsedAthlete[] */
    public $athletes;
    /** @var ParsedAthlete */
    public $athlete;

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
        $athlete = $this->athlete ?? Arr::first($this->athletes);
        $record = EventRecord::getCached($eventId, $athlete->gender);
        if (!$record) {
            return 0;
        }

        $recordTime = Cleaner::cleanTime($record->time);

        return (int)(1000 * (($recordTime->totalSeconds / $this->time->totalSeconds) ** 3));
    }

    abstract public function saveToDatabase(): void;
}
