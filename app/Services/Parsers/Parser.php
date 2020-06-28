<?php

namespace App\Services\Parsers;

use App\Competition;
use App\Services\ParsedObjects\ParsedCompetition;
use ParseError;

abstract class Parser
{
    /** @var Parser */
    private static $instance;
    /** @var string */
    protected $competition;
    /** @var ParserConfig */
    public $config;
    /** @var ParsedCompetition */
    protected $parsedCompetition;

    private const FILE_EXTENSION_PARSER_MAPPINGS = [
        'pdf' => TextParser::class,
        'lxf' => LenexParser::class,
    ];

    public static function getInstance(Competition $competition): Parser
    {
        $file = $competition->getFirstMediaPath('results_file');
        if (!(self::$instance instanceof self)) {
            $fileExtension = pathinfo($file, PATHINFO_EXTENSION);

            if (!isset(self::FILE_EXTENSION_PARSER_MAPPINGS[$fileExtension])) {
                throw new ParseError($fileExtension . ' is not supported');
            }

            $parserType = self::FILE_EXTENSION_PARSER_MAPPINGS[$fileExtension];
            self::$instance = $parserType::getInstance($competition);
        }
        return self::$instance;
    }

    public function __construct(Competition $competition)
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

    abstract public function getRawData(): string;

    abstract protected function parse(): void;
}
