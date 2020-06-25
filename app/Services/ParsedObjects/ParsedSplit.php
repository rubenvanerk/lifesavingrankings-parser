<?php

namespace App\Services\ParsedObjects;

use Carbon\CarbonInterval;
use ParseError;

class ParsedSplit implements ParsedObject
{
    /** @var CarbonInterval */
    public $time;
    /** @var int */
    public $distance;

    public function __construct(CarbonInterval $time, int $distance)
    {
        if ($distance % 50 !== 0) {
            throw new ParseError('Distance should be divisible by 50, given distance: ' . $distance);
        }
        $this->time = $time;
        $this->distance = $distance;
    }

    public function getTimeStringForDisplay(): string
    {
        return str_replace('0000', '', $this->time->format('%I:%S.%F'));
    }

    public function saveToDatabase(): void
    {
        // TODO: Implement saveToDatabase() method.
    }
}
