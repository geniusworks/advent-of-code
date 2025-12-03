<?php
/**
 * Advent of Code 2025
 * Day 3: Lobby
 *
 * @author Martin Diekhoff
 * @link https://adventofcode.com/2025/day/3
 */

const DATA_INPUT_FILE = 'input03.txt';

require_once __DIR__ . '/../bootstrap.php';

$input = DataImporter::importFromFileWithDefaultFlags(__DIR__ . '/' . DATA_INPUT_FILE);

function maxBankJoltage(string $bank): int
{
    $bank = trim($bank);
    $length = strlen($bank);

    if ($length < 2) {
        return 0;
    }

    $first = array_fill(0, 10, null);
    $last = array_fill(0, 10, null);

    for ($i = 0; $i < $length; $i++) {
        $ch = $bank[$i];
        if ($ch < '0' || $ch > '9') {
            continue;
        }

        $digit = (int) $ch;
        if ($first[$digit] === null) {
            $first[$digit] = $i;
        }
        $last[$digit] = $i;
    }

    $best = 0;

    for ($tens = 9; $tens >= 0; $tens--) {
        if ($first[$tens] === null) {
            continue;
        }

        $tensPos = $first[$tens];

        for ($ones = 9; $ones >= 0; $ones--) {
            if ($last[$ones] !== null && $last[$ones] > $tensPos) {
                $best = 10 * $tens + $ones;
                return $best;
            }
        }
    }

    return $best;
}

function solvePart1(array $input)
{
    $total = 0;

    foreach ($input as $line) {
        $line = trim($line);
        if ($line === '') {
            continue;
        }

        $total += maxBankJoltage($line);
    }

    return $total;
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
