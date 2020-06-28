<?php

namespace App\Services\ParsedObjects;

use App\Competition;
use Illuminate\Support\Str;

class ParsedCompetition implements ParsedObject
{
    /** @var Competition */
    public $model;
    /** @var ParsedResult[] */
    public $results = [];

    private const STATUS_IMPORTED = 2;

    public function __construct(Competition $competition)
    {
        $this->model = $competition;
    }

    public function saveToDatabase(): void
    {
        $competitionSlug = Str::slug($this->name);
        $competition = Competition::firstOrCreate(
            ['slug' => $competitionSlug],
            [
                'name' => $this->name,
                'date' => $this->date,
                'location' => $this->location,
                'type_of_timekeeping' => $this->timekeeping,
                'is_concept' =>  true,
                'file_name' => 'filename', // TODO
                'credit' => $this->credit ?: null,
                'status' => self::STATUS_IMPORTED,
            ]
        );
        self::$model = $competition;

        foreach ($this->results as $result) {
            $result->saveToDatabase();
        }
    }
}
