<?php

namespace App\Services\ParsedObjects;

use App\CompetitionConfig;
use Illuminate\Support\Str;

class ParsedCompetition implements ParsedObject
{
    /** @var CompetitionConfig */
    public static $model;
    /** @var ParsedResult[] */
    public $results = [];

    private const STATUS_IMPORTED = 2;

    public function __construct(CompetitionConfig $competition)
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
