<?php

namespace App\Services\ParsedObjects;

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

    public function __construct(?CarbonInterval $time, ParsedAthlete $athlete, int $round, bool $disqualified,
                                bool $didNotStart, bool $withdrawn, ?string $originalLine, ?int $heat, ?int $lane, ?CarbonInterval $reactionTime, ?array $splits)
    {
        if (!$disqualified && !$didNotStart && !$withdrawn && is_null($time)) {
            throw new \ParseError('Time can not be null if DSQ, DNS and WDR are false');
        }
        if ($disqualified + $didNotStart + $withdrawn > 1) {
            throw new \ParseError(
                sprintf('Only one of DSQ, DNS or WDR can be true. Given values: DSQ: %b, DNS: %b, WDR: %b',
                    $disqualified, $didNotStart, $withdrawn)
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

    public function saveToDatabase()
    {
        // TODO: Implement saveToDatabase() method.
    }
}
