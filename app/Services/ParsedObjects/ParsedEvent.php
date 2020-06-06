<?php

namespace App\Services\ParsedObjects;

class ParsedEvent implements ParsedObject {
    public $id;

    public const EVENT_NAMES = [
        1 => '100m manikin carry with fins',
        2 => '50m manikin carry',
        3 => '200m obstacle swim',
        4 => '100m manikin tow with fins',
        5 => '100m rescue medley',
        6 => '200m super lifesaver',
        7 => '50m obstacle swim',
        8 => '50m free style with fins',
        12 => '25m manikin carry',
        13 => '50m free style with tube',
        18 => '100m obstacle swim'
    ];

    public function __construct(int $id)
    {
        $this->id = $id;
    }

    public function addResult(ParsedResult $result): void
    {
        $this->results[] = $result;
    }

    public function getName(): string
    {
        return self::EVENT_NAMES[$this->id] ?? (string) $this->id;
    }
}
