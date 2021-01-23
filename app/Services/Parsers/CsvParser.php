<?php

namespace App\Services\Parsers;

use App\CompetitionConfig;
use League\Csv\HTMLConverter;
use League\Csv\Reader;

class CsvParser extends Parser
{
    private static ?Parser $instance = null;
    public static function getInstance(CompetitionConfig $competition): Parser
    {
        if (!(self::$instance instanceof self)) {
            self::$instance = new self($competition);
        }

        return self::$instance;
    }

    public function getRawData(): string
    {
        $resultsFile = $this->competition->getFirstMediaPath('results_file');
        $csv = Reader::createFromPath($resultsFile);
        $csv->setHeaderOffset(0);
        return (new HTMLConverter())->table('table')->convert($csv, $csv->getHeader());
    }

    protected function parse(): void
    {
        // TODO: Implement parse() method.
    }
}
