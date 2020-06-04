<?php

namespace App\Services\Cleaners;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class HytekCleaner extends Cleaner
{
    private const TIME_THREE_LINES_UP_FROM_TIME_TYPE = 'three_lines_up_from_time';

    public static function cleanText(string $text, ?string $type): string
    {
        if ($type === self::TIME_THREE_LINES_UP_FROM_TIME_TYPE) {
            return self::threeLinesUp($text);
        }
        return $text;
    }

    private static function threeLinesUp(string $text): string
    {
        $lines = explode("\n", $text);
        $newLines = [];
        $i = 0;
        foreach ($lines as $line) {
            if (preg_match('/(\d:)?\d{2}\.\d{2}/', $line) !== 1
                && !Str::contains($line, 'DQ')) {
                $newLines[] = $line;
                $i++;
                continue;
            }
            $resultLine = '';
            $resultLine .= Arr::pull($newLines, $i - 3) . "\t";
            $resultLine .= Arr::pull($newLines, $i - 2) . "\t";
            $resultLine .= Arr::pull($newLines, $i - 1) . "\t";
            $resultLine .= $lines[$i] . "\t";
            $newLines[] = $resultLine;
            $i++;
        }
        return implode("\n", $newLines);
    }
}
