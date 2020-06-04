<?php

namespace App\Services\Cleaners;

class JauswertungCleaner extends Cleaner
{
    private const NAME_19_LINES_DOWN = 'name_19_lines_down';

    public static function cleanText(string $text, ?string $type): string
    {
        if ($type === self::NAME_19_LINES_DOWN) {
            return self::name19LinesDown($text);
        }
        return $text;
    }

    private static function name19LinesDown(string $text): string
    {

    }
}
