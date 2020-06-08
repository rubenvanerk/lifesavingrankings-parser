<?php

namespace App\Services\ParsedObjects;

use App\Competition;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class ParsedCompetition implements ParsedObject {
    public static $model;
    public $name;
    public $location;
    public $date;
    public $timekeeping;
    public $credit;
    public $resultCount = 0;
    /**
     * @var ParsedIndividualResult[]
     */
    public $results = [];

    private CONST STATUS_IMPORTED = 2;

    public function __construct(string $name, string $location, string $date, int $timekeeping, string $credit)
    {
        $this->name = $name;
        $this->location = $location;
        $this->date = $date;
        $this->timekeeping = $timekeeping;
        $this->credit = $credit;
    }

    public function saveToDatabase()
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
                'status' => self::STATUS_IMPORTED
            ]
        );
        self::$model = $competition;

        foreach ($this->results as $result) {
            $result->saveToDatabase();
        }
    }
}
