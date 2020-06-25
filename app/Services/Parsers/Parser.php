<?php

namespace App\Services\Parsers;

use App\Services\ParsedObjects\ParsedCompetition;
use ParseError;

abstract class Parser
{
    /** @var Parser */
    private static $instance;
    /** @var string */
    protected $fileName;
    /** @var ParserConfig */
    public $config;
    /** @var ParsedCompetition */
    protected $parsedCompetition;

    private const FILE_EXTENSION_PARSER_MAPPINGS = [
        'pdf' => TextParser::class,
        'lxf' => LenexParser::class,
    ];

    public static function getInstance(string $file): Parser
    {
        if (!(self::$instance instanceof self)) {
            $fileExtension = pathinfo($file, PATHINFO_EXTENSION);

            if (!isset(self::FILE_EXTENSION_PARSER_MAPPINGS[$fileExtension])) {
                throw new ParseError($fileExtension . ' is not supported');
            }

            $parserType = self::FILE_EXTENSION_PARSER_MAPPINGS[$fileExtension];
            self::$instance = $parserType::getInstance($file);
        }
        return self::$instance;
    }

    public function __construct(string $fileName)
    {
        $this->fileName = $fileName;
        $this->config = new ParserConfig($this->fileName);
    }

    public function getParsedCompetition(): ParsedCompetition
    {
        $this->parsedCompetition = new ParsedCompetition(
            $this->config->{'info.name'},
            $this->config->{'info.location'},
            $this->config->{'info.date'},
            $this->config->{'info.timekeeping'},
            $this->config->{'info.credit'}
        );
        $this->parse();
        return $this->parsedCompetition;
    }

    abstract public function getRawData(): string;

    abstract protected function parse(): void;
}
