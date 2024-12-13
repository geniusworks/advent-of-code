<?php
/**
 * Advent of Code 2024
 * Day 13: Claw Contraption
 *
 * @author Martin Diekhoff
 * @link https://adventofcode.com/2024/day/13
 */

const DATA_INPUT_FILE = 'input13.txt';

require_once __DIR__ . '/../' . 'bootstrap.php';

$input = DataImporter::importFromFileWithDefaultFlags(__DIR__ . '/' . DATA_INPUT_FILE);

function parseInput($input): array
{
    $machines = [];
    $machineStrings = preg_split('/\s*\n\s*\n\s*/', implode("\n", $input));

    foreach ($machineStrings as $machineString) {
        $lines = preg_split('/\R/', trim($machineString));

        if (count($lines) < 3) {
            continue;
        }

        // Extract button and prize information
        preg_match('/Button A: X\+(\d+), Y\+(\d+)/', $lines[0], $buttonAMatch);
        preg_match('/Button B: X\+(\d+), Y\+(\d+)/', $lines[1], $buttonBMatch);
        preg_match('/Prize: X=(\d+), Y=(\d+)/', $lines[2], $prizeMatch);

        if (count($buttonAMatch) < 3 || count($buttonBMatch) < 3 || count($prizeMatch) < 3) {
            continue;
        }

        $machines[] = [
            'buttonA' => [intval($buttonAMatch[1]), intval($buttonAMatch[2])],
            'buttonB' => [intval($buttonBMatch[1]), intval($buttonBMatch[2])],
            'prize' => [intval($prizeMatch[1]), intval($prizeMatch[2])],
        ];
    }
    return $machines;
}

function findMinimumTokens($machine)
{
    $buttonA = $machine['buttonA'];
    $buttonB = $machine['buttonB'];
    $prize = $machine['prize'];
    $minTokens = PHP_INT_MAX;

    for ($a = 0; $a <= 100; $a++) {
        for ($b = 0; $b <= 100; $b++) {
            // Check if the button presses exactly match the prize coordinates
            if ($a * $buttonA[0] + $b * $buttonB[0] == $prize[0] &&
                $a * $buttonA[1] + $b * $buttonB[1] == $prize[1]) {
                // Calculate total token cost (3 tokens for A, 1 token for B)
                $currentTokens = $a * 3 + $b;
                $minTokens = min($minTokens, $currentTokens);
            }
        }
    }

    return $minTokens == PHP_INT_MAX ? null : $minTokens;
}

function solvePart1($input)
{
    $machines = parseInput($input);
    $totalTokens = 0;

    foreach ($machines as $machine) {
        $minTokens = findMinimumTokens($machine);
        if ($minTokens !== null) {
            $totalTokens += $minTokens;
        }
    }

    return $totalTokens;
}

// Part 1
$profiler = new Profiler('Part 1');
$profiler->startProfile();
$result1 = solvePart1($input);
$profiler->stopProfile();
echo "Result = {$result1}" . PHP_EOL;
$profiler->reportProfile();

// Part 2 (placeholder)
$profiler = new Profiler('Part 2');
$profiler->startProfile();
$result2 = null; // TODO: Implement Part 2
$profiler->stopProfile();
echo "Result = {$result2}" . PHP_EOL;
$profiler->reportProfile();