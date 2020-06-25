<?php

namespace App\Services\ParsedObjects;

use App\Competition;
use Illuminate\Support\Str;

class ParsedCompetition implements ParsedObject
{
    /** @var Competition */
    public static $model;
    /** @var string  */
    public $name;
    /** @var string  */
    public $location;
    /** @var string  */
    public $date;
    /** @var int  */
    public $timekeeping;
    /** @var string  */
    public $credit;
    /** @var ParsedResult[] */
    public $results = [];

    private const STATUS_IMPORTED = 2;

    public function __construct(string $name, string $location, string $date, int $timekeeping, string $credit)
    {
        $this->name = $name;
        $this->location = $location;
        $this->date = $date;
        $this->timekeeping = $timekeeping;
        $this->credit = $credit;
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
                'status' => self::STATUS_IMPORTED
            ]
        );
        self::$model = $competition;

        foreach ($this->results as $result) {
            $result->saveToDatabase();
        }
    }
}
