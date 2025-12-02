<?php
/**
 * Advent of Code 2025
 * Day 1: Secret Entrance
 *
 * @author Martin Diekhoff
 * @link https://adventofcode.com/2025/day/1
 */

const DATA_INPUT_FILE = 'input01.txt';

require_once __DIR__ . '/../bootstrap.php';

$input = DataImporter::importFromFileWithDefaultFlags(__DIR__ . '/' . DATA_INPUT_FILE);

function simulateDial(array $input): array
{
    $position = 50; // starting position
    $hitsAtZero = 0;

    foreach ($input as $line) {
        $line = trim($line);
        if ($line === '') {
            continue;
        }

        $direction = $line[0];
        $distance = (int) substr($line, 1);
        $steps = $distance % 100; // dial has values 0-99

        if ($direction === 'L') {
            $position = ($position - $steps) % 100;
        } elseif ($direction === 'R') {
            $position = ($position + $steps) % 100;
        } else {
            continue;
        }

        if ($position < 0) {
            $position += 100;
        }

        if ($position === 0) {
            $hitsAtZero++;
        }
    }

    return [$position, $hitsAtZero];
}

function solvePart1(array $input)
{
    [$finalPosition, ] = simulateDial($input);

    return $finalPosition;
}

function solvePart2(array $input)
{
    [, $hitsAtZero] = simulateDial($input);

    return $hitsAtZero;
}

// Part 1

$profiler = new Profiler();
$profiler->startProfile();
$result1 = solvePart1($input);
$profiler->stopProfile();
echo "Result: {$result1}" . PHP_EOL;
$profiler->reportProfile();

// Part 2

$profiler = new Profiler();
$profiler->startProfile();
$result2 = solvePart2($input);
$profiler->stopProfile();
echo "Result: {$result2}" . PHP_EOL;
$profiler->reportProfile();
