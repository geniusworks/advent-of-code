<?php
/**
 * Advent of Code 2025
 * Day 5: Cafeteria
 *
 * @author Martin Diekhoff
 * @link https://adventofcode.com/2025/day/5
 */

const DATA_INPUT_FILE = 'input05.txt';

require_once __DIR__ . '/../bootstrap.php';

$input = DataImporter::importFromFileWithDefaultFlags(__DIR__ . '/' . DATA_INPUT_FILE);

function parseDatabase(array $input): array
{
    $ranges = [];
    $ids = [];

    $i = 0;
    $count = count($input);

    while ($i < $count) {
        $line = trim($input[$i]);
        if ($line === '') {
            $i++;
            break;
        }

        [$startStr, $endStr] = explode('-', $line);
        $start = (int) $startStr;
        $end = (int) $endStr;

        if ($start > $end) {
            [$start, $end] = [$end, $start];
        }

        $ranges[] = [$start, $end];
        $i++;
    }

    while ($i < $count) {
        $line = trim($input[$i]);
        if ($line !== '') {
            $ids[] = (int) $line;
        }
        $i++;
    }

    return [$ranges, $ids];
}

function mergeRanges(array $ranges): array
{
    if (empty($ranges)) {
        return [];
    }

    usort($ranges, function ($a, $b) {
        if ($a[0] === $b[0]) {
            return $a[1] <=> $b[1];
        }
        return $a[0] <=> $b[0];
    });

    $merged = [];
    [$curStart, $curEnd] = $ranges[0];

    $n = count($ranges);
    for ($i = 1; $i < $n; $i++) {
        [$start, $end] = $ranges[$i];

        if ($start <= $curEnd) {
            if ($end > $curEnd) {
                $curEnd = $end;
            }
        } else {
            $merged[] = [$curStart, $curEnd];
            $curStart = $start;
            $curEnd = $end;
        }
    }

    $merged[] = [$curStart, $curEnd];

    return $merged;
}

function isFresh(int $id, array $mergedRanges): bool
{
    $lo = 0;
    $hi = count($mergedRanges) - 1;

    while ($lo <= $hi) {
        $mid = intdiv($lo + $hi, 2);
        [$start, $end] = $mergedRanges[$mid];

        if ($id < $start) {
            $hi = $mid - 1;
        } elseif ($id > $end) {
            $lo = $mid + 1;
        } else {
            return true;
        }
    }

    return false;
}

function solvePart1(array $input)
{
    [$ranges, $ids] = parseDatabase($input);

    if (empty($ranges) || empty($ids)) {
        return 0;
    }

    $merged = mergeRanges($ranges);

    $freshCount = 0;
    foreach ($ids as $id) {
        if (isFresh($id, $merged)) {
            $freshCount++;
        }
    }

    return $freshCount;
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
