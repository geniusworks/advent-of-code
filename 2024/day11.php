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
    $stoneTree = [];
    foreach ($stones as $stone) {
        if ($stone == 0) {
            $stoneTree[1] = ($stoneTree[1] ?? 0) + 1;
        } elseif (strlen((string)$stone) % 2 == 0) {
            $half = strlen((string)$stone) / 2;
            $left = (int)substr((string)$stone, 0, $half);
            $right = (int)substr((string)$stone, $half);
            $stoneTree[$left] = ($stoneTree[$left] ?? 0) + 1;
            $stoneTree[$right] = ($stoneTree[$right] ?? 0) + 1;
        } else {
            $stoneTree[$stone * 2024] = ($stoneTree[$stone * 2024] ?? 0) + 1;
        }
    }
    return $stoneTree;
}

function simulateBlinks($stoneData, $numBlinks): int
{
    $stoneTree = [];
    $stones = array_map('intval', explode(' ', trim($stoneData)));
    foreach ($stones as $stone) {
        $stoneTree[$stone] = ($stoneTree[$stone] ?? 0) + 1;
    }
    for ($i = 1; $i <= $numBlinks; $i++) {
        $newStoneTree = [];
        foreach ($stoneTree as $stone => $count) {
            if ($stone == 0) {
                $newStoneTree[1] = ($newStoneTree[1] ?? 0) + $count;
            } elseif (strlen((string)$stone) % 2 == 0) {
                $half = strlen((string)$stone) / 2;
                $left = (int)substr((string)$stone, 0, $half);
                $right = (int)substr((string)$stone, $half);
                $newStoneTree[$left] = ($newStoneTree[$left] ?? 0) + $count;
                $newStoneTree[$right] = ($newStoneTree[$right] ?? 0) + $count;
            } else {
                $newStoneTree[$stone * 2024] = ($newStoneTree[$stone * 2024] ?? 0) + $count;
            }
        }
        $stoneTree = $newStoneTree;
    }
    return array_sum($stoneTree);
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

$blinkCount = 75;
$profiler = new Profiler('Part 2');
$profiler->startProfile();
$result1 = simulateBlinks($input[0], $blinkCount);
$profiler->stopProfile();
echo "Number of stones after blinking {$blinkCount} times: {$result1}" . PHP_EOL;
$profiler->reportProfile();