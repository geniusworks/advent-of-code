<?php
/**
 * Advent of Code 2025
 * Day 2: Gift Shop
 *
 * @author Martin Diekhoff
 * @link https://adventofcode.com/2025/day/2
 */

const DATA_INPUT_FILE = 'input02.txt';

require_once __DIR__ . '/../bootstrap.php';

$input = DataImporter::importFromFileWithDefaultFlags(__DIR__ . '/' . DATA_INPUT_FILE);

function parseRanges(string $line): array
{
    $ranges = [];
    $parts = explode(',', trim($line));

    foreach ($parts as $part) {
        $part = trim($part);
        if ($part === '') {
            continue;
        }

        [$startStr, $endStr] = explode('-', $part);
        $start = (int) $startStr;
        $end = (int) $endStr;

        if ($start > $end) {
            [$start, $end] = [$end, $start];
        }

        $ranges[] = [$start, $end];
    }

    return $ranges;
}

function solvePart1(array $input)
{
    if (empty($input)) {
        return 0;
    }

    $line = trim($input[0]);
    if ($line === '') {
        return 0;
    }

    $ranges = parseRanges($line);
    if (empty($ranges)) {
        return 0;
    }

    $globalMin = PHP_INT_MAX;
    $globalMax = PHP_INT_MIN;

    foreach ($ranges as [$start, $end]) {
        if ($start < $globalMin) {
            $globalMin = $start;
        }
        if ($end > $globalMax) {
            $globalMax = $end;
        }
    }

    $maxDigits = strlen((string) $globalMax);
    $maxHalfDigits = intdiv($maxDigits, 2);

    $sum = 0;

    $startBase = 1;
    for ($k = 1; $k <= $maxHalfDigits; $k++) {
        $endBase = $startBase * 10;

        for ($base = $startBase; $base < $endBase; $base++) {
            $candidate = (int) ($base . $base);

            if ($candidate < $globalMin || $candidate > $globalMax) {
                continue;
            }

            foreach ($ranges as [$start, $end]) {
                if ($candidate >= $start && $candidate <= $end) {
                    $sum += $candidate;
                    break;
                }
            }
        }

        $startBase = $endBase;
    }

    return $sum;
}

function solvePart2(array $input)
{
    return null;
}

$profiler = new Profiler();
$profiler->startProfile();
$result1 = solvePart1($input);
$profiler->stopProfile();
echo "Result: {$result1}" . PHP_EOL;
$profiler->reportProfile();

$profiler = new Profiler();
$profiler->startProfile();
$result2 = solvePart2($input);
$profiler->stopProfile();
echo "Result: {$result2}" . PHP_EOL;
$profiler->reportProfile();
