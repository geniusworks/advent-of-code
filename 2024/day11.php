<?php
/**
 * Advent of Code 2024
 * Day 11:
 *
 * @author Martin Diekhoff
 * @link https://adventofcode.com/2024/day/11
 */

const DATA_INPUT_FILE = 'input11.txt';

require_once __DIR__ . '/../' . 'bootstrap.php';

$input = DataImporter::importFromFileWithDefaultFlags(__DIR__ . '/' . DATA_INPUT_FILE);

function blink($stones): array
{
    $newStones = [];
    foreach ($stones as $stone) {
        if ($stone == 0) {
            $newStones[] = 1;
        } elseif (strlen((string)$stone) % 2 == 0) {
            $half = strlen((string)$stone) / 2;
            $newStones[] = (int)substr((string)$stone, 0, $half);
            $newStones[] = (int)substr((string)$stone, $half);
        } else {
            $newStones[] = $stone * 2024;
        }
    }
    return $newStones;
}

function simulateBlinks($stoneData, $numBlinks): int
{
    $stones = array_map('intval', explode(' ', trim($stoneData)));
    for ($i = 1; $i <= $numBlinks; $i++) {
        $stones = blink($stones);
        // echo "After blink " . $i . ": " . implode(' ', $stones) . "\n";
    }
    return count($stones);
}

// Part 1

$blinkCount = 25;
$profiler = new Profiler('Part 1');
$profiler->startProfile();
$result1 = simulateBlinks($input[0], $blinkCount);
$profiler->stopProfile();
echo "Number of stones after blinking {$blinkCount} times: {$result1}" . PHP_EOL;
$profiler->reportProfile();

// Part 2
/*
$profiler = new Profiler('Part 2');
$profiler->startProfile();
$result2 = null; // TODO: Calculate the result for part 2.
$profiler->stopProfile();
echo "Result = {$result2}" . PHP_EOL;
$profiler->reportProfile();
*/