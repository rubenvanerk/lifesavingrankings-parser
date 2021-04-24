<?php

namespace App\Services\Parsers;

use App\CompetitionConfig;
use App\Services\Cleaners\Cleaner;
use App\Services\ParsedObjects\ParsedAthlete;
use App\Services\ParsedObjects\ParsedIndividualResult;
use League\Csv\HTMLConverter;
use League\Csv\Reader;

class CsvParser extends Parser
{
    private static ?Parser $instance = null;
    private Reader $csvReader;

    public static function getInstance(CompetitionConfig $competition): Parser
    {
        if (!(self::$instance instanceof self)) {
            $instance = new self($competition);

            $resultsFile = $instance->competition->getFirstMedia('results_file');
            $instance->csvReader = Reader::createFromStream($resultsFile->stream());
            $instance->csvReader->setDelimiter($instance->config->{'csv_settings.delimiter'} ?: ';');
            $instance->csvReader->setHeaderOffset(0);

            $instance->setCsvColumnOptions();
            $instance->setEventNameOptions();

            self::$instance = $instance;
        }

        return self::$instance;
    }

    public function getRawData(bool $returnText = false): string
    {
        return (new HTMLConverter())->table('table table-bordered table-fixed')
            ->convert($this->csvReader, $this->csvReader->getHeader());
    }

    protected function parse(): void
    {
        foreach ($this->csvReader as $record) {
            $parsedResult = $this->parseFromRecord($record);
            $this->parsedCompetition->results[] = $parsedResult;
        }
    }

    /**
     * Dynamically create column options from the csv header
     */
    private function setCsvColumnOptions(): void
    {
        $columns = array_combine($this->csvReader->getHeader(), $this->csvReader->getHeader());
        array_unshift($columns, '');
        $template = $this->config->template;
        foreach ($template['csv_columns'] as $key => $configColumn) {
            $template['csv_columns'][$key]['options'] = $columns;
        }
        $this->config->template = $template;
    }

    /**
     * Dynamically create event name options from csv unique values
     */
    private function setEventNameOptions(): void
    {
        if (!$this->config->{'csv_columns.event'}) {
            return;
        }

        $eventNames = [''];
        foreach ($this->csvReader as $record) {
            if (!isset($record[$this->config->{'csv_columns.event'}])) {
                continue;
            }
            if (!in_array($record[$this->config->{'csv_columns.event'}], $eventNames)) {
                $eventNames[] = $record[$this->config->{'csv_columns.event'}];
            }
        }
        $eventNames = array_combine($eventNames, $eventNames);

        $template = $this->config->template;
        foreach ($template['events']['event_names'] as $key => $configColumn) {
            $template['events']['event_names'][$key]['options'] = $eventNames;
        }
        $this->config->template = $template;
    }

    private function parseFromRecord($record): ParsedIndividualResult
    {
        $time = $record[$this->config->{'csv_columns.time'}];
        if ($this->config->{'results.dsq'} && preg_match($this->config->{'results.dsq'}, $time)) {
            $disqualified = true;
            $time = null;
        } else {
            $time = Cleaner::cleanTime($time);
        }
        $parsedAthlete = $this->getParsedAthleteFromRecord($record);

        $parsedResult = new ParsedIndividualResult(
            $time,
            $parsedAthlete,
            0, // TODO: implement round
            $disqualified ?? false,
            false, // TODO: implement dsq
            false, // TODO: implement withdrawn
            implode(', ', $record),
            null, // TODO: implement heat
            null, // TODO: implement lane
            null, // TODO: implement reaction time
            null, // TODO implement splits
        );

        $parsedResult->eventId = $this->getEventIdFromRecord($record);

        return $parsedResult;
    }

    private function getParsedAthleteFromRecord($record): ParsedAthlete
    {
        if ($this->config->{'csv_columns.first_name'} && $this->config->{'csv_columns.last_name'}) {
            $name = $record[$this->config->{'csv_columns.first_name'}] . ' ' . $record[$this->config->{'csv_columns.last_name'}];
        } else {
            $name = $record[$this->config->{'csv_columns.athlete'}];
        }

        $name = Cleaner::cleanName(
            $name,
            $this->config->{'athlete.first_name_regex'},
            $this->config->{'athlete.last_name_regex'}
        );
        $gender = Cleaner::cleanGender($record[$this->config->{'csv_columns.gender'}]);

        $team = $this->config->{'csv_columns.team'} ? $record[$this->config->{'csv_columns.team'}] : null;

        return new ParsedAthlete(
            $name,
            null, // TODO: implement yob
            $gender,
            null, // TODO: implement nationality
            $team
        );
    }

    private function getEventIdFromRecord($record)
    {
        $eventNames = $this->config->{'events.event_names'};
        return array_search($record[$this->config->{'csv_columns.event'}], $eventNames);
    }
}
