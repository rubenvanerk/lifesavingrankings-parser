<?php

namespace App\Services\ParsedObjects;

use Carbon\CarbonInterval;

class ParsedRelayResult extends ParsedResult
{
    public function __construct(?CarbonInterval $time, array $athletes, int $round, bool $disqualified,
                                bool $didNotStart, bool $withdrawn, ?string $originalLine, ?int $heat, ?int $lane,
                                ?CarbonInterval $reactionTime, ?array $splits)
    {
        if (!$disqualified && !$didNotStart && !$withdrawn && is_null($time)) {
            throw new \ParseError('Time can not be null if DSQ, DNS and WDR are false');
        }
        if ((int)$disqualified + (int)$didNotStart + (int)$withdrawn > 1) {
            throw new \ParseError(
                sprintf('Only one of DSQ, DNS or WDR can be true. Given values: DSQ: %b, DNS: %b, WDR: %b',
                    $disqualified, $didNotStart, $withdrawn)
            );
        }
        $this->time = $time;
        $this->athletes = $athletes;
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
        // TODO: Implement saveToDatabase() method.
    }
}
