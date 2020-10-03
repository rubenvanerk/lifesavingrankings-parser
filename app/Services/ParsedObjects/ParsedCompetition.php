<?php

namespace App\Services\ParsedObjects;

use App\Competition;
use App\CompetitionConfig;

class ParsedCompetition implements ParsedObject
{
    public static CompetitionConfig $competitionConfig;
    public static Competition $competition;
    public array $results = [];

    private const STATUS_IMPORTED = 2;

    public function __construct(CompetitionConfig $competition)
    {
        self::$competitionConfig = $competition;
    }

    public function saveToDatabase(): void
    {
        self::$competition = self::$competitionConfig->saveCompetition();

        /** @var ParsedResult $result */
        foreach ($this->results as $result) {
            $result->saveToDatabase();
        }
    }
}
