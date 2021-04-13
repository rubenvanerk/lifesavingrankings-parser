<?php

namespace App\Services\ParsedObjects;

use App\Athlete;
use App\Country;
use App\Participation;
use App\Team;
use Carbon\Carbon;
use Exception;
use ParseError;

class ParsedAthlete implements ParsedObject
{
    public string $name;
    public ?int $yearOfBirth;
    public int $gender;
    public ?string $nationality;
    public ?string $team;

    public const MALE = 1;
    public const FEMALE = 2;

    public function __construct(string $name, ?int $yearOfBirth, int $gender, ?string $nationality, ?string $team)
    {
        if (!is_null($yearOfBirth) && ($yearOfBirth < 1900 || $yearOfBirth > Carbon::now()->year)) {
            throw new ParseError('Invalid year of birth: ' . $yearOfBirth);
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
            ->where('gender', $this->gender);

        if ($this->yearOfBirth) {
            $athlete->where('year_of_birth', $this->yearOfBirth);
        }

        $athlete = $athlete->first();

        if (!$athlete) {
            $athlete = Athlete::create([
                'name' => $this->name,
                'gender' => $this->gender,
                'year_of_birth' => $this->yearOfBirth,
            ]);
        }

        if ($athlete->aliasOf) {
            $athlete = $athlete->aliasOf;
        }

        if ($this->team) {
            $team = Team::firstOrCreate([
                'name' => $this->team,
            ]);

            $competition = ParsedCompetition::$competition;

            $participation = Participation::whereHas('team', function ($query) use ($team) {
                $query->where('id', $team->id);
            })->whereHas('athlete', function ($query) use ($athlete) {
                $query->where('id', $athlete->id);
            })->whereHas('competition', function ($query) use ($competition) {
                $query->where('id', $competition->id);
            })->first();

            if (!$participation) {
                $participation = new Participation();
                $participation->athlete()->associate($athlete);
                $participation->competition()->associate($competition);
                $participation->team()->associate($team);
                $participation->save();
            }
        }

        if (!$this->nationality) {
            return $athlete;
        }

        $nationality = Country::where('lenex_code', $this->nationality)->first();
        if (!$nationality) {
            throw new Exception('Nationality with code ' . $this->nationality . ' not found!');
        }

        if (!$athlete->nationalities()->where('lenex_code', $this->nationality)->first()) {
            $athlete->nationalities()->save($nationality);
            $athlete->save();
        }

        return $athlete;
    }
}
