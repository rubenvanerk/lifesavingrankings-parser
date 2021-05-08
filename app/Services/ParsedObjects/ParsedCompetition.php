<?php

namespace App\Services\ParsedObjects;

use App\Competition;
use App\CompetitionConfig;
use App\IndividualResult;
use Illuminate\Support\Facades\DB;

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
        self::$competition = self::$competitionConfig->competition;

        self::$competition->individual_results()->delete();

        $insertValues = [];

        /** @var ParsedIndividualResult $result */
        foreach ($this->results as $result) {
            $insertValues[] = $result->getInsertValues();
        }

        Db::connection('rankings')->table('rankings_individualresult')->insert($insertValues);
    }
}
