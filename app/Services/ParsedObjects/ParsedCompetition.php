<?php

namespace App\Services\ParsedObjects;

use App\Competition;
use Illuminate\Support\Str;

class ParsedCompetition implements ParsedObject
{
    /** @var Competition */
    public static $model;
    /** @var ParsedResult[] */
    public $results = [];

    private const STATUS_IMPORTED = 2;

    public function __construct(Competition $competition)
    {
        self::$model = $competition;
    }

    public function saveToDatabase(): void
    {
        foreach ($this->results as $result) {
            $result->saveToDatabase();
        }
    }
}
