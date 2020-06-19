<?php

namespace App\Services\ParsedObjects;

use Carbon\CarbonInterval;

class ParsedSplit implements ParsedObject
{
    public $time;
    public $distance;

    public function __construct(CarbonInterval $time, int $distance)
    {
        if ($distance % 50 !== 0) {
            throw new \ParseError('Distance should be divisible by 50, given distance: ' . $distance);
        }
        $this->time = $time;
        $this->distance = $distance;
    }

    public function getTimeStringForDisplay()
    {
        if (!$this->time) {
            return null;
        }
        return str_replace('0000', '', $this->time->format('%I:%S.%F'));
    }

    public function saveToDatabase()
    {
        // TODO: Implement saveToDatabase() method.
    }
}
