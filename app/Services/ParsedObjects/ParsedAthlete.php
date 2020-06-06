<?php

namespace App\Services\ParsedObjects;

use App\Athlete;
use App\Nationality;
use Carbon\Carbon;

class ParsedAthlete implements ParsedObject {
    public $name;
    public $yearOfBirth;
    public $gender;
    public $nationality;
    public $team;

    public const MALE = 1;
    public const FEMALE = 2;

    public function __construct(string $name, ?int $yearOfBirth, int $gender, ?string $nationality, ?string $team)
    {
        if (!is_null($yearOfBirth) && ($yearOfBirth < 1900 || $yearOfBirth > Carbon::now()->year)) {
            throw new \ParseError('Invalid year of birth: ' . $yearOfBirth);
        }
        $this->name = $name;
        $this->yearOfBirth = $yearOfBirth;
        $this->gender = $gender;
        $this->nationality = $nationality;
        $this->team = $team;
    }

    public function saveToDatabase(): Athlete
    {
        $athlete = Athlete::where('name', 'ilike', $this->name)
            ->where('gender', $this->gender)
            ->where('year_of_birth', $this->yearOfBirth)
            ->first();

        if (!$athlete) {
            $athlete = Athlete::create([
                'name' => $this->name,
                'gender' => $this->gender,
                'year_of_birth' => $this->yearOfBirth
            ]);
        }

        // TODO: check if athlete is alias of another athlete

        $athlete->save();

        if (!$this->nationality) {
            return $athlete;
        }

        $nationality = Nationality::where('lenex_code', $this->nationality)->first();
        if (!$nationality) {
            throw new \Exception('Nationality with code ' . $this->nationality . ' not found!');
        }

        if (!$athlete->nationalities()->where('lenex_code', $this->nationality)->first()) {
            $athlete->nationalities()->save($nationality);
            $athlete->save();
        }

        return $athlete;
    }
}
