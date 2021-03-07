<?php

namespace App\Services\Parsers;

use App\CompetitionConfig;
use App\Services\ParsedObjects\ParsedCompetition;
use ParseError;

abstract class Parser
{
    private static ?Parser $instance = null;
    protected CompetitionConfig $competition;
    public ParserConfig $config;
    protected ParsedCompetition $parsedCompetition;

    private const FILE_EXTENSION_PARSER_MAPPINGS = [
        'pdf' => TextParser::class,
        'lxf' => LenexParser::class,
        'csv' => CsvParser::class,
    ];

    public static function getInstance(CompetitionConfig $competition): Parser
    {
        if (!(self::$instance instanceof self)) {
            $fileType = $competition->getFileType();

            if (!isset(self::FILE_EXTENSION_PARSER_MAPPINGS[$fileType])) {
                throw new ParseError($fileType . ' is not supported');
            }

            $parserType = self::FILE_EXTENSION_PARSER_MAPPINGS[$fileType];
            self::$instance = $parserType::getInstance($competition);
        }
        return self::$instance;
    }

    public function __construct(CompetitionConfig $competition)
    {
        $this->competition = $competition;
        $this->config = new ParserConfig($this->competition);
    }

    public function getParsedCompetition(): ParsedCompetition
    {
        $this->parsedCompetition = new ParsedCompetition($this->competition);
        $this->parse();
        return $this->parsedCompetition;
    }

    public function getFileExtension()
    {
        return $this->competition->getFileType();
    }

    abstract public function getRawData(): string;

    /**
     *  The parse method fills $this->parsedCompetition with results
     */
    abstract protected function parse(): void;
}
