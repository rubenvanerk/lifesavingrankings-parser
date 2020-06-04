<?php

namespace App\Services\Parsers;

use App\Services\ParsedObjects\ParsedCompetition;
use App\Services\Parsers\LenexParser;
use App\Services\Parsers\TextParser;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

abstract class Parser {
    private static $instance;
    protected $fileName;
    public $config;
    protected $parsedCompetition;

    private const FILE_EXTENSION_PARSER_MAPPINGS = [
        'pdf' => TextParser::class,
        'lxf' => LenexParser::class,
    ];

    public static function getInstance($file): Parser
    {
        if (!(self::$instance instanceof self)) {
            $fileExtension = pathinfo(storage_path('app/' . $file), PATHINFO_EXTENSION);
            if (!isset(self::FILE_EXTENSION_PARSER_MAPPINGS[$fileExtension])) {
                throw new \ParseError($fileExtension . ' is not supported');
            }
            $parserType = self::FILE_EXTENSION_PARSER_MAPPINGS[$fileExtension];
            self::$instance = $parserType::getInstance($file);
        }
        return self::$instance;
    }

    public function __construct($fileName)
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

    abstract public function getRawData();

    abstract protected function parse();
}
