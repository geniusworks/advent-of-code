<?php
/**
 * Advent of Code 2024
 * Day 2: Red-Nosed Reports
 *
 * @author Martin Diekhoff
 * @link https://adventofcode.com/2024/day/2
 */

require_once '../bootstrap.php';

$input = DataImporter::importFromFileWithDefaultFlags('input02.txt');

function isValid($levels): bool
{
    $isIncreasing = true;
    $isDecreasing = true;

    for ($i = 1; $i < count($levels); $i++) {
        $diff = abs($levels[$i] - $levels[$i - 1]);

        if ($diff < 1 || $diff > 3) {
            return false;
        }

        if ($levels[$i] > $levels[$i - 1]) {
            $isDecreasing = false;
        } elseif ($levels[$i] < $levels[$i - 1]) {
            $isIncreasing = false;
        }
    }

    return $isIncreasing || $isDecreasing;
}

// Part 1

$profiler = new Profiler('Part 1');
$profiler->startProfile();

$safeReports = 0;

foreach ($input as $line) {
    $levels = array_map('intval', explode(' ', $line));

    if (isValid($levels)) {
        $safeReports++;
    }
}

$profiler->stopProfile();
echo "Number of valid reports: {$safeReports}" . PHP_EOL;
$profiler->reportProfile();

// Part 2

$profiler = new Profiler('Part 2');
$profiler->startProfile();

$safeReports = 0;

foreach ($input as $line) {
    $levels = array_map('intval', explode(' ', $line));

    foreach ($levels as $j => $level) {
        $newLevels = array_merge(array_slice($levels, 0, $j), array_slice($levels, $j + 1));

        if (isValid($newLevels)) {
            $safeReports++;
            break;
        }
    }
}

$profiler->stopProfile();
echo "Number of valid reports: {$safeReports}" . PHP_EOL;
$profiler->reportProfile();