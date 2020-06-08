<?php

namespace App\Services\Cleaners;

use Carbon\CarbonInterval;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

abstract class Cleaner
{
    abstract public static function cleanText(string $text, ?string $type): string;

    /**
     * @param string $text
     * @param array $lineCombiner
     * @return string
     * For every line in $text that matches with pattern in $lineCombiner,
     * the $amount of lines int $direction wil be combined into one line, separated by the delimiter
     */
    public static function combineLines(string $text, array $lineCombiner): string
    {
        $pattern = $lineCombiner['pattern'];
        $direction = $lineCombiner['direction'];
        $amount = $lineCombiner['amount'];
        $delimiter = $lineCombiner['delimiter'];

        if (!$pattern || !$direction || !$amount) {
            return $text;
        }

        $lines = explode(PHP_EOL, $text);
        $newLines = [];
        $i = 0;
        $skipNextAmountOfLines = 0;
        foreach ($lines as $line) {
            if ($skipNextAmountOfLines-- > 0) {
                $i++;
                continue;
            }
            if (preg_match($pattern, $line) !== 1) {
                $newLines[] = $line;
                $i++;
                continue;
            }

            $newLine = $line;

            if ($direction === 'up') {
                for ($j = 1; $j <= $amount; $j++) {
                    $index = $i - $j;
                    $newLine .= self::translateQuoted($delimiter) . Arr::pull($newLines, $index);
                }
            }

            if ($direction === 'down') {
                for ($j = 1; $j < $amount; $j++) {
                    $index = $i + $j;
                    $newLine .= self::translateQuoted($delimiter) . Arr::get($lines, $index);
                }
                $skipNextAmountOfLines = $amount;
            }

            $newLines[] = $newLine;
            $i++;
        }
        return implode("\n", $newLines);
    }

    /**
     * @param string $text
     * @param array $customReplaces
     * @return string
     *
     * This function takes an array in the form of [$pattern => $replace]
     * and replaces each occurrence of $pattern with $replace
     */
    public static function customReplace(string $text, array $customReplaces): string
    {
        foreach ($customReplaces as $pattern => $replace) {
            if ($pattern) {
                $replace = self::translateQuoted($replace);
                $text = preg_replace($pattern, $replace, $text);
            }
        }

        return $text;
    }

    /**
     * @param string $text
     * @param array $classCleaners
     * @return string
     * This function takes an array in the form of [$class => $type]
     * and calls the clean function on the $class with $type as type argument
     */
    public static function applyClassCleaners(string $text, array $classCleaners): string
    {
        /** @var Cleaner $class */
        foreach ($classCleaners as $class => $type) {
            $class = 'App\\Services\\Cleaners\\' . $class;
            $text = $class::cleanText($text, $type);
        }

        return $text;
    }

    /**
     * @param string $text
     * @param array $lineMovers
     * @return string
     * This function takes an array in the form of ['/pattern/>>/pattern/>>direction']
     * Then goes through the text line by line until it finds the first pattern
     * Then it searches in the given direction for the second pattern
     * When the second pattern is found, that line is replaced with the line found by the first pattern
     */
    public static function moveLines(string $text, array $lineMovers): string
    {
        $lines = explode(PHP_EOL, $text);
        foreach ($lineMovers as $lineMover) {
            if (!$lineMover) {
                continue;
            }

            [$searchPattern, $replacePattern, $direction] = explode('>>', $lineMover);

            $i = -1;
            foreach ($lines as $line) {
                $i++;
                if (preg_match($searchPattern, $line) !== 1) {
                    continue;
                }

                if ($direction === 'up') {
                    $incrementer = -1;
                } elseif ($direction === 'down') {
                    $incrementer = 1;
                } else {
                    throw new \ParseError('Linemover direction should be \'up\' or \'down\' (' . $direction . ')');
                }


                $j = $i;
                while ($j > 0 && $j < count($lines)) {
                    $j += $incrementer;
                    if (preg_match($replacePattern, $lines[$j]) !== 1) {
                        continue;
                    }
                    $lines[$j] = $line;
                    break;
                }

                $lines[$i] = '';

            }
        }

        return implode(PHP_EOL, $lines);
    }

    public static function cleanName(string $name): string
    {
        $nameParts = explode(',', $name);

        if (count($nameParts) > 2) {
            throw new \ParseError('Got more name parts then expected for ' . $name);
        }

        $nameParts = array_map('trim', $nameParts);

        if (count($nameParts) === 2) {
            $nameParts = array_reverse($nameParts);
            $name = implode(' ', $nameParts);
        }

        return $name;
    }

    public static function cleanYearOfBirth(int $yearOfBirth, int $competitionYear): int
    {
        $minimumYearOfBirth = $competitionYear - 100;

        // year of birth can be in YY format, add 100s until in acceptable range
        while ($yearOfBirth < $minimumYearOfBirth && $yearOfBirth < $competitionYear) {
            $yearOfBirth += 100;
        }

        return $yearOfBirth;
    }

    public static function cleanTime(string $time): CarbonInterval
    {
        $time = trim($time);
        $time = Str::replaceLast(',', '.', $time);

        preg_match_all('/\d{1,2}(?=:)/', $time, $minutes);
        $minutes = Arr::first($minutes);
        $minutes = (int) Arr::last($minutes) ?: 0;

        preg_match('/\d{2}(?=\.)/', $time, $seconds);
        $seconds = Arr::first($seconds);

        preg_match('/(?<=\.)\d{2}/', $time, $centiSeconds);
        $microseconds = (int) Arr::first($centiSeconds) * 10000;

        return CarbonInterval::minutes($minutes)->seconds($seconds)->microseconds($microseconds);
    }

    public static function translateQuoted($string)
    {
        $search  = array("\\t", "\\n", "\\r");
        $replace = array( "\t",  "\n",  "\r");
        return str_replace($search, $replace, $string);
    }
}
