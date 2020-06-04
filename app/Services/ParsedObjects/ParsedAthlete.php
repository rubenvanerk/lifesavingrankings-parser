<?php

namespace App\Services\ParsedObjects;

use Carbon\Carbon;

class ParsedAthlete implements ParsedObject {
    public $name;
    public $yearOfBirth;
    public $gender;
    public $nationality;
    public $club;

    public const MALE = 1;
    public const FEMALE = 2;

    public function __construct(string $name, ?int $yearOfBirth, int $gender, ?string $nationality, ?string $club)
    {
        if (!is_null($yearOfBirth) && ($yearOfBirth < 1900 || $yearOfBirth > Carbon::now()->year)) {
            throw new \ParseError('Invalid year of birth: ' . $yearOfBirth);
        }
        $this->name = $name;
        $this->yearOfBirth = $yearOfBirth;
        $this->gender = $gender;
        $this->nationality = $nationality;
        $this->club = $club;
    }

    public function saveToDatabase()
    {
        // TODO: Implement saveToDatabase() method.
    }
}
