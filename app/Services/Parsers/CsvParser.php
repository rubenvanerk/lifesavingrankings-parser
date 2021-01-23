<?php

namespace App\Services\Parsers;

use App\CompetitionConfig;
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

            $resultsFile = $instance->competition->getFirstMediaPath('results_file');
            $instance->csvReader = Reader::createFromPath($resultsFile);
            $instance->csvReader->setHeaderOffset(0);

            $instance->setColumnOptions();

            self::$instance = $instance;
        }

        return self::$instance;
    }

    public function getRawData(): string
    {
        return (new HTMLConverter())->table('table')->convert($this->csvReader, $this->csvReader->getHeader());
    }

    protected function parse(): void
    {
        // TODO: Implement parse() method.
    }

    private function setColumnOptions()
    {
        $columns = array_combine($this->csvReader->getHeader(), $this->csvReader->getHeader());
        $config = $this->config;
        foreach ($config->columns as $key => $configColumn) {
            $config->{'columns.' . $key . '.options'} = $columns;
        }
        $this->config = $config;
    }
}
