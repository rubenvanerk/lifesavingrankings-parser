<?php

namespace App\Services\Parsers;

use App\Services\Cleaners;
use App\Services\Cleaners\Cleaner;
use App\Services\ParsedObjects\ParsedAthlete;
use App\Services\ParsedObjects\ParsedCompetition;
use App\Services\ParsedObjects\ParsedResult;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

class TextParser extends Parser
{
    /** @var Parser */
    private static $_instance;
    /** @var string */
    protected $fileName;
    /** @var ParserConfig */
    public $config;
    /** @var int */
    private $currentEventId;
    /** @var int */
    private $currentGender;
    /** @var int */
    private $currentRound;
    /** @var boolean */
    private $currentEventRejected;
    /**
     * @var ParsedCompetition $parsedCompetition
     */
    protected $parsedCompetition;

    private const EVENT_LINE_TYPE = 'event';
    private const RESULT_LINE_TYPE = 'result';
    private const REJECT_EVENT_LINE_TYPE = 'reject_event';
    private const DSQ_LINE_TYPE = 'dsq';
    private const DNS_LINE_TYPE = 'dns';
    private const SEPARATE_GENDER_LINE_TYPE = 'separate_gender';

    public static function getInstance(string $file): Parser
    {
        if (!(self::$_instance instanceof self)) {
            self::$_instance = new self($file);
        }

        return self::$_instance;
    }

    public function getRawData(): string
    {
        return $this->getText();
    }

    private function getText(): string
    {
        $parser = new \Smalot\PdfParser\Parser();
        if (config('filesystems.default') === 's3') {
            $pdf = $parser->parseFile(Storage::temporaryUrl($this->fileName, Carbon::now()->addMinutes(5)));
        } else {
            $pdf = $parser->parseFile(Storage::path($this->fileName));
        }
        if ($this->config->{'pdfparser_options'}) {
            \Smalot\PdfParser\Parser::$horizontalOffset =
                $this->translateQuoted($this->config->{'pdfparser_options.horizontal_offset'}) ?: ' ';
        }
        $text = $pdf->getText();
        return $this->cleanText($text);
    }

    private function cleanText(string $text): string
    {
        $text = Cleaners\Cleaner::combineLines($text, $this->config->{'cleaning_options.line_combiner'});
        $text = Cleaners\Cleaner::customReplace($text, $this->config->getTextAreaAsArray('cleaning_options.custom_replace'));
//        $text = Cleaners\Cleaner::applyClassCleaners($text, $this->config->{'cleaning_options.class_cleaners'});
        $text = Cleaners\Cleaner::moveLines($text, explode(PHP_EOL, $this->config->{'cleaning_options.line_movers'}));

        return htmlspecialchars_decode($text, ENT_QUOTES);
    }

    private function getLines(): array
    {
        return explode(PHP_EOL, $this->getText());
    }

    protected function parse(): void
    {
        foreach ($this->getLines() as $line) {
            $lineType = $this->getLineType($line);
            switch ($lineType) {
                case self::REJECT_EVENT_LINE_TYPE:
                    $this->currentEventRejected = true;
                    break;
                case self::EVENT_LINE_TYPE:
                    $this->currentEventId = $this->getEventIdFromLine($line);
                    $this->currentGender = $this->getGenderFromLine($line);
                    $this->currentEventRejected = false;
                    break;
                case self::RESULT_LINE_TYPE:
                    if ($this->currentEventRejected) {
                        break;
                    }
                    $results = $this->getResultsFromLine($line);
                    foreach ($results as $result) {
                        $this->parsedCompetition->results[] = $result;
                    }
                    break;
                case self::SEPARATE_GENDER_LINE_TYPE:
                    $this->currentGender = $this->getGenderFromLine($line);
                    break;
                case self::DSQ_LINE_TYPE:
                case self::DNS_LINE_TYPE:
                    if ($this->currentEventRejected) {
                        break;
                    }
                    $this->parsedCompetition->results[] = $this->getInvalidatedResultFromLine($line, $lineType);
                    break;
            }
        }
    }

    private function getLineType(string $line): string
    {
        if ($this->config->{'events.event_rejector'}
            && preg_match($this->config->{'events.event_rejector'}, $line) === 1
            && (
                !$this->config->{'events.event_designifier'}
                || preg_match($this->config->{'events.event_designifier'}, $line) === 0
            )
        ) {
            return self::REJECT_EVENT_LINE_TYPE;
        }

        if (preg_match($this->config->{'events.event_signifier'}, $line) === 1
            && (
                !$this->config->{'events.event_designifier'}
                || preg_match($this->config->{'events.event_designifier'}, $line) === 0
            )
        ) {
            return self::EVENT_LINE_TYPE;
        }

        if (preg_match($this->config->{'results.time'}, $line) === 1
            && (
                !$this->config->{'results.result_rejector'}
                || preg_match($this->config->{'results.result_rejector'}, $line) === 0
            )
        ) {
            return self::RESULT_LINE_TYPE;
        }

        if ($this->config->{'genders.separate_gender_signifier'} && preg_match($this->config->{'genders.separate_gender_signifier'}, $line) === 1) {
            return self::SEPARATE_GENDER_LINE_TYPE;
        }

        if ($this->config->{'results.dsq'} && preg_match($this->config->{'results.dsq'}, $line) === 1) {
            return self::DSQ_LINE_TYPE;
        }

        if ($this->config->{'results.dns'} && preg_match($this->config->{'results.dns'}, $line) === 1) {
            return self::DNS_LINE_TYPE;
        }

        return '';
    }

    private function getEventIdFromLine(string $line): int
    {
        foreach ($this->config->{'events.event_names'} as $eventId => $eventRegex) {
            if (preg_match($eventRegex, $line) === 1) {
                return $eventId;
            }
        }
        throw new \ParseError(sprintf('Could not find event in line \'%s\'', $line));
    }

    private function getGenderFromLine(string $line): int
    {
        if (preg_match($this->config->{'genders.women'}, $line) === 1) {
            return ParsedAthlete::FEMALE;
        }

        if (preg_match($this->config->{'genders.men'}, $line) === 1) {
            return ParsedAthlete::MALE;
        }

        throw new \ParseError(sprintf('Could not find gender in line \'%s\'', $line));
    }

    private function getResultsFromLine(string $line): array
    {
        if ($this->config->{'as_csv.as_csv'}) {
            try {
                return $this->getResultsByCsv($line);
            } catch (\ErrorException $errorException) {
                throw new \ParseError($errorException->getMessage() . ': ' . $line);
            }
        }

        return $this->getResultsByPatterns($line);
    }

    private function getResultsByCsv(string $line): array
    {
        $csv = str_getcsv($line, $this->config->{'as_csv.delimiter'});
        $name = $csv[$this->config->{'as_csv.indexes.name'}];
        $name = Cleaner::cleanName($name);
        $yearOfBirth = (int)$csv[$this->config->{'as_csv.indexes.yob'}];
        $competitionYear = (int)Carbon::create($this->config->{'info.date'})->year;
        $yearOfBirth = Cleaner::cleanYearOfBirth($yearOfBirth, $competitionYear);
        $team = $this->config->{'as_csv.indexes.team'} ? $csv[$this->config->{'as_csv.indexes.team'}] : null;

        $parsedAthlete = new ParsedAthlete(
            $name,
            $yearOfBirth,
            $this->currentGender,
            null,
            $team
        );

        $parsedResults = [];
        foreach ($this->config->{'as_csv.indexes.events'} as $eventId => $csvIndex) {
            if (!$csvIndex || !$csv[$csvIndex] || empty(trim($csv[$csvIndex]))) {
                continue;
            }
            $parsedResult = new ParsedResult(
                Cleaner::cleanTime($csv[$csvIndex]),
                $parsedAthlete,
                0,
                false,
                false,
                false,
                $line,
                null,
                null,
                null,
                null
            );
            $parsedResult->eventId = $eventId;
            $parsedResults[] = $parsedResult;
        }
        return $parsedResults;
    }

    private function getResultsByPatterns(string $line): array
    {
        $athlete = $this->getAthleteFromLine($line);
        $times = $this->getTimesFromLine($line);
        $roundFromLine = $this->getRoundFromLine($line);
        $heat = $this->getHeatFromLine($line);
        $disqualified = false;
        $didNotStart = false;
        $withdrawn = false;
        $originalLine = $line;

        $parsedResults = [];
        $loopIndex = 0;
        foreach ($times as $time) {
            $parsedResult = new ParsedResult(
                Cleaner::cleanTime($time),
                $athlete,
                $roundFromLine ?? $this->currentRound ?? $loopIndex,
                $disqualified,
                $didNotStart,
                $withdrawn,
                $originalLine,
                $heat,
                null,
                null,
                null
            );
            $parsedResult->eventId = $this->currentEventId;
            $parsedResults[] = $parsedResult;
            $loopIndex++;
        }

        if (count($parsedResults) === 0) {
            throw new \ParseError(sprintf('Time(s) not found in line \'%s\'', $line));
        }

        return $parsedResults;
    }

    private function getInvalidatedResultFromLine(string $line, string $type): ParsedResult
    {
        $athlete = $this->getAthleteFromLine($line);
        $roundFromLine = $this->getRoundFromLine($line);
        $heat = $this->getHeatFromLine($line);
        $disqualified = $type === self::DSQ_LINE_TYPE;
        $didNotStart = $type === self::DNS_LINE_TYPE;
        $withdrawn = false;
        $originalLine = $line;

        $parsedResult = new ParsedResult(
            null,
            $athlete,
            $roundFromLine ?? $this->currentRound ?? 0,
            $disqualified,
            $didNotStart,
            $withdrawn,
            $originalLine,
            $heat,
            null,
            null,
            null
        );
        $parsedResult->eventId = $this->currentEventId;
        return $parsedResult;
    }

    private function getAthleteFromLine(string $line): ParsedAthlete
    {
        if (!isset($this->currentGender)) {
            throw new \ParseError('Can not parse athlete without current gender');
        }

        $name = $this->getNameFromLine($line);
        $yearOfBirth = $this->getYearOfBirthFromLine($line);
        $nationality = $this->getNationalityFromLine($line);
        $team = $this->getTeamFromLine($line);

        return new ParsedAthlete($name, $yearOfBirth, $this->currentGender, $nationality, $team);
    }

    private function getNameFromLine(string $line): string
    {
        preg_match($this->config->{'athlete.name'}, $line, $matches);
        return Cleaner::cleanName(Arr::first($matches));
    }

    private function getYearOfBirthFromLine(string $line): ?int
    {
        if (!$this->config->{'athlete.yob'}) {
            return null;
        }
        preg_match($this->config->{'athlete.yob'}, $line, $matches);
        $yearOfBirth = (int)Arr::first($matches);
        $competitionYear = (int)Carbon::create($this->config->{'info.date'})->year;
        return Cleaner::cleanYearOfBirth($yearOfBirth, $competitionYear);
    }

    public function getNationalityFromLine(string $line): ?string // should return IOC country code
    {
        $pattern = $this->config->{'athlete.nationality'};
        if (!$pattern) {
            return null;
        }
        preg_match($pattern, $line, $matches);
        return Arr::first($matches);
    }

    public function getTeamFromLine(string $line): ?string
    {
        $pattern = $this->config->{'athlete.team'};
        if (!$pattern) {
            return null;
        }
        preg_match($pattern, $line, $matches);
        return Arr::first($matches);
    }

    private function getTimesFromLine(string $line): array
    {
        preg_match_all($this->config->{'results.time'}, $line, $matches);
        $times = Arr::first($matches);
        $timeIndex = $this->config->{'results.time_index'};
        switch ($timeIndex) {
            case 'all':
                return $times;
            case 'first':
                return [Arr::first($times)];
            case 'last':
                return [Arr::last($times)];
            default:
                return [$times[$timeIndex]];
        }
    }

    private function getRoundFromLine(string $line): ?int
    {
        $pattern = $this->config->{'results.round'};
        if (!$pattern) {
            return null;
        }
        preg_match($pattern, $line, $matches);
        $roundAsDisplayed = Arr::first($matches);
        if (!$roundAsDisplayed) {
            return null;
        }
        $roundMappings = $this->config->getTextAreaAsArray('results.round_mappings');
        return $roundMappings[$roundAsDisplayed];
    }

    private function getHeatFromLine(string $line): ?int
    {
        $pattern = $this->config->{'results.heat'};
        if (!$pattern) {
            return null;
        }
        preg_match($pattern, $line, $matches);
        return Arr::first($matches);
    }

    private function translateQuoted(string $string): string
    {
        $search = array("\\t", "\\n", "\\r");
        $replace = array("\t", "\n", "\r");
        return str_replace($search, $replace, $string);
    }
}
