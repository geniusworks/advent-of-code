<?php
/**
 * Advent of Code 2025
 * Day 9: Movie Theater
 */

const DATA_INPUT_FILE = 'input09.txt';

require_once __DIR__ . '/../bootstrap.php';

$input = DataImporter::importFromFileWithDefaultFlags(__DIR__ . '/' . DATA_INPUT_FILE);

function solvePart1(array $input)
{
    $points = [];

    foreach ($input as $line) {
        $line = trim($line);
        if ($line === '') {
            continue;
        }

        $parts = explode(',', $line);
        if (count($parts) !== 2) {
            continue;
        }

        $x = (int) $parts[0];
        $y = (int) $parts[1];

        $points[] = [$x, $y];
    }

    $n = count($points);
    if ($n < 2) {
        return 0;
    }

    $maxArea = 0;

    for ($i = 0; $i < $n; $i++) {
        $pi = $points[$i];
        for ($j = $i + 1; $j < $n; $j++) {
            $pj = $points[$j];

            $width = abs($pi[0] - $pj[0]) + 1;
            $height = abs($pi[1] - $pj[1]) + 1;

            $area = $width * $height;
            if ($area > $maxArea) {
                $maxArea = $area;
            }
        }
    }

    return $maxArea;
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
