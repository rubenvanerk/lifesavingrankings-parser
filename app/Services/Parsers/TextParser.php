<?php

namespace App\Services\Parsers;

use App\CompetitionConfig;
use App\Event;
use App\Services\Cleaners;
use App\Services\Cleaners\Cleaner;
use App\Services\ParsedObjects\ParsedAthlete;
use App\Services\ParsedObjects\ParsedCompetition;
use App\Services\ParsedObjects\ParsedIndividualResult;
use App\Services\ParsedObjects\ParsedRelayResult;
use App\Services\ParsedObjects\ParsedResult;
use Carbon\Carbon;
use ErrorException;
use Illuminate\Support\Arr;
use League\Csv\HTMLConverter;
use League\Csv\Reader;
use ParseError;

class TextParser extends Parser
{
    private static ?Parser $instance = null;
    protected CompetitionConfig $competition;
    public ParserConfig $config;
    private int $currentEventId;
    private int $currentGender;
    private int $currentRound;
    private bool $currentEventRejected;
    protected ParsedCompetition $parsedCompetition;

    private const EVENT_LINE_TYPE = 'event';
    private const RESULT_LINE_TYPE = 'result';
    private const REJECT_EVENT_LINE_TYPE = 'reject_event';
    private const DSQ_LINE_TYPE = 'dsq';
    private const DNS_LINE_TYPE = 'dns';
    private const WITHDRAWN_LINE_TYPE = 'withdrawn';
    private const SEPARATE_GENDER_LINE_TYPE = 'separate_gender';

    public static function getInstance(CompetitionConfig $competition): Parser
    {
        if (!(self::$instance instanceof self)) {
            self::$instance = new self($competition);
        }

        return self::$instance;
    }

    public function getRawData(): string
    {
        $text = $this->getText();
        if (!$this->config->{'as_csv.as_csv'}) {
            return $text;
        }

        $text = preg_replace('/' . $this->config->{'as_csv.delimiter'} . '/', ';', $text);
        $csvReader = Reader::createFromString($text);
        $csvReader->setDelimiter(';');

        $longestRow = 0;
        foreach ($csvReader as $item) {
            if (sizeof($item) > $longestRow) {
                $longestRow = sizeof($item);
            }
        }

        return (new HTMLConverter())->table('table table-bordered table-fixed')
            ->convert($csvReader, range(0, $longestRow - 1));
    }

    private function getText(): string
    {
        $parser = new \Smalot\PdfParser\Parser();
        if (config('media-library.disk_name') === 's3') {
            $pdfFile = $this->competition->getFirstMedia('results_file');
            $pdf = $parser->parseFile($pdfFile->getTemporaryUrl(Carbon::now()->addMinute()));
        } else {
            $pdfFile = $this->competition->getFirstMediaPath('results_file');
            $pdf = $parser->parseFile($pdfFile);
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

        $text =  htmlspecialchars_decode($text, ENT_QUOTES);
        $text = str_replace("\xc2\xa0", ' ', $text);
        return $text;
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
                    if (!$this->currentEventId) {
                        throw new ParseError('Cannot parse result when no event is active. Line: ' . $line);
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
                case self::WITHDRAWN_LINE_TYPE:
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

        if ($this->config->{'results.withdrawn'} && preg_match($this->config->{'results.withdrawn'}, $line) === 1) {
            return self::WITHDRAWN_LINE_TYPE;
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
        throw new ParseError(sprintf('Could not find event in line \'%s\'', $line));
    }

    private function getGenderFromLine(string $line): int
    {
        if (preg_match($this->config->{'genders.women'}, $line) === 1) {
            return ParsedAthlete::FEMALE;
        }

        if (preg_match($this->config->{'genders.men'}, $line) === 1) {
            return ParsedAthlete::MALE;
        }

        throw new ParseError(sprintf('Could not find gender in line \'%s\'', $line));
    }

    private function getResultsFromLine(string $line): array
    {
        $eventModel = Event::find($this->currentEventId);

        if ($eventModel === null) {
            throw new ParseError(sprintf('Could not find Event(%s)', $this->currentEventId));
        }

        if ($eventModel->type === Event::EVENT_TYPE_RELAY) {
            return $this->getRelayResultFromLine($line);
        }

        return $this->getIndividualResultsFromLine($line);
    }

    private function getRelayResultFromLine(string $line): array
    {
        $parsedAthletes = $this->getAthletesFromLine($line);
        $times = $this->getTimesFromLine($line);
        $roundFromLine = $this->getRoundFromLine($line);
        $heat = $this->getHeatFromLine($line);
        $disqualified = false;
        $didNotStart = false;
        $withdrawn = false;

        $parsedRelayResult = new ParsedRelayResult(
            Cleaner::cleanTime(Arr::first($times)),
            $parsedAthletes,
            $roundFromLine ?? $this->currentRound ?? 0,
            $disqualified,
            $didNotStart,
            $withdrawn,
            $line,
            $heat,
            null,
            null,
            null
        );
        return [$parsedRelayResult];
    }

    private function getIndividualResultsFromLine(string $line): array
    {
        if ($this->config->{'as_csv.as_csv'}) {
            try {
                return $this->getResultsByCsv($line);
            } catch (ErrorException $errorException) {
                throw new ParseError($errorException->getMessage() . ': ' . $line);
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
        $competitionYear = Carbon::create($this->config->{'info.date'})->year;
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
            $parsedResult = new ParsedIndividualResult(
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
            $parsedResult = new ParsedIndividualResult(
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
            return [];
            throw new ParseError(sprintf('Time(s) not found in line \'%s\'', $line));
        }

        return $parsedResults;
    }

    private function getInvalidatedResultFromLine(string $line, string $type): ParsedResult
    {
        $eventModel = Event::find($this->currentEventId);

        if ($eventModel === null) {
            throw new ParseError(sprintf('Could not find Event(%s)', $this->currentEventId));
        }

        if ($eventModel->type === Event::EVENT_TYPE_RELAY) {
            return $this->getInvalidatedRelayResultFromLine($line, $type);
        }

        return $this->getInvalidatedIndividualResultFromLine($line, $type);
    }

    private function getInvalidatedRelayResultFromLine(string $line, string $type): ParsedRelayResult
    {
        $athletes = $this->getAthletesFromLine($line);
        $roundFromLine = $this->getRoundFromLine($line);
        $heat = $this->getHeatFromLine($line);
        $disqualified = $type === self::DSQ_LINE_TYPE;
        $didNotStart = $type === self::DNS_LINE_TYPE;
        $withdrawn = false;
        $originalLine = $line;

        $parsedResult = new ParsedRelayResult(
            null,
            $athletes,
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

    private function getInvalidatedIndividualResultFromLine(string $line, string $type): ParsedIndividualResult
    {
        $athlete = $this->getAthleteFromLine($line);
        $roundFromLine = $this->getRoundFromLine($line);
        $heat = $this->getHeatFromLine($line);
        $disqualified = $type === self::DSQ_LINE_TYPE;
        $didNotStart = $type === self::DNS_LINE_TYPE;
        $withdrawn = $type === self::WITHDRAWN_LINE_TYPE;
        $originalLine = $line;

        $parsedResult = new ParsedIndividualResult(
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

    private function getAthletesFromLine(string $line): array
    {
        $names = $this->getNamesFromLine($line);
        $team = $this->getTeamFromLine($line);

        $parsedAthletes = [];

        foreach ($names as $name) {
            $name = Cleaner::cleanName($name);
            $parsedAthletes[] = new ParsedAthlete(
                $name,
                null,
                $this->currentGender,
                null,
                $team
            );
        }

        return $parsedAthletes;
    }

    private function getNamesFromLine(string $line): array
    {
        $names = [];
        if ($this->config->{'athlete.names_explode'}) {
            preg_match($this->config->{'athlete.names'}, $line, $names);
            $names = explode($this->config->{'athlete.names_explode'}, Arr::first($names));
        }
        return $names;
    }

    private function getAthleteFromLine(string $line): ParsedAthlete
    {
        if (!isset($this->currentGender)) {
            throw new ParseError('Can not parse athlete without current gender');
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
        $name = Arr::first($matches);

        if (is_null($name)) {
            throw new ParseError(sprintf('Could not find name in line %s', $line));
        }

        return Cleaner::cleanName(Arr::first($matches));
    }

    private function getYearOfBirthFromLine(string $line): ?int
    {
        if (!$this->config->{'athlete.yob'}) {
            return null;
        }
        preg_match($this->config->{'athlete.yob'}, $line, $matches);
        $yearOfBirth = (int)Arr::first($matches);
        $competitionYear = Carbon::create($this->config->{'info.date'})->year;
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
                if (isset($times[$timeIndex])) {
                    return [$times[$timeIndex]];
                }
                throw new ParseError('Could not find time in line: ' . $line);

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
        $search = ['\\t', '\\n', '\\r'];
        $replace = ["\t", "\n", "\r"];
        return str_replace($search, $replace, $string);
    }
}
