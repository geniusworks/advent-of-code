<?php
/**
 * Advent of Code 2024
 * Day 22: Monkey Market
 *
 * @author Martin Diekhoff
 * @link https://adventofcode.com/2024/day/22
 */

const DATA_INPUT_FILE = 'input22.txt';

require_once __DIR__ . '/../' . 'bootstrap.php';

$input = DataImporter::importFromFileWithDefaultFlags(__DIR__ . '/' . DATA_INPUT_FILE);

function solvePart1($input): int
{
    $sum = 0;

    foreach ($input as $index => $buyer) {
        $secretNumber = (int)$buyer;
        for ($i = 0; $i < 2000; $i++) {
            $secretNumber = ($secretNumber * 64) ^ $secretNumber;
            $secretNumber %= 16777216;

            $temp = (int)($secretNumber / 32);
            $secretNumber = $temp ^ $secretNumber;
            $secretNumber %= 16777216;

            $secretNumber = ($secretNumber * 2048) ^ $secretNumber;
            $secretNumber %= 16777216;
        }
        // echo ($index + 1) . ": " . $secretNumber . "\n";
        $sum += $secretNumber;
    }

    return $sum;
}

function solvePart2($input)
{
    // @todo: Solve part 2
}

// Part 1

$profiler = new Profiler();
$profiler->startProfile();
$result1 = solvePart1($input);
$profiler->stopProfile();
echo "The sum of the 2000th secret number generated by each buyer: {$result1}" . PHP_EOL;
$profiler->reportProfile();

// Part 2

$profiler = new Profiler();
$profiler->startProfile();
$result2 = solvePart2($input); // TODO: Calculate the result for part 2.
$profiler->stopProfile();
echo "Result: {$result2}" . PHP_EOL;
$profiler->reportProfile();