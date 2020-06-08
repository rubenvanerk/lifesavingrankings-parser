<?php

namespace App\Services\ParsedObjects;

use App\EventRecord;
use App\Services\Cleaners\Cleaner;
use Illuminate\Support\Arr;

abstract class ParsedResult implements ParsedObject
{
    public $time;
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
        $athlete = $this->athlete ?? Arr::first($this->athletes);
        $record = EventRecord::whereHas('event', function ($query) use ($eventId, $athlete) {
            return $query->where('id', $eventId)->where('gender', $athlete->gender);
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
}
