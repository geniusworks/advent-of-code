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

function parseInput($input, $addOffset = false): array
{
    $machines = [];
    $machineStrings = preg_split('/\s*\n\s*\n\s*/', implode("\n", $input));

    foreach ($machineStrings as $machineString) {
        $lines = preg_split('/\R/', trim($machineString));

        if (count($lines) < 3) {
            continue;
        }

        preg_match('/Button A: X\+(-?\d+), Y\+(-?\d+)/', $lines[0], $buttonAMatch);
        preg_match('/Button B: X\+(-?\d+), Y\+(-?\d+)/', $lines[1], $buttonBMatch);
        preg_match('/Prize: X=(-?\d+), Y=(-?\d+)/', $lines[2], $prizeMatch);

        if (count($buttonAMatch) < 3 || count($buttonBMatch) < 3 || count($prizeMatch) < 3) {
            continue;
        }

        $offset = $addOffset ? '10000000000000' : '0';

        $machines[] = [
            'buttonA' => [(string)($buttonAMatch[1]), (string)($buttonAMatch[2])],
            'buttonB' => [(string)($buttonBMatch[1]), (string)($buttonBMatch[2])],
            'prize' => [
                $addOffset ? bcadd($prizeMatch[1], $offset) : $prizeMatch[1],
                $addOffset ? bcadd($prizeMatch[2], $offset) : $prizeMatch[2],
            ],
        ];
    }
    return $machines;
}

function findMinimumTokens($machine): ?string
{
    $buttonA = $machine['buttonA'];
    $buttonB = $machine['buttonB'];
    $prize = $machine['prize'];

    $det = bcsub(bcmul($buttonA[0], $buttonB[1]), bcmul($buttonB[0], $buttonA[1]));

    if (bccomp($det, '0') == 0) {
        return null; // No solution
    }

    $countA = bcdiv(bcsub(bcmul($prize[0], $buttonB[1]), bcmul($buttonB[0], $prize[1])), $det);
    $countB = bcdiv(bcsub(bcmul($buttonA[0], $prize[1]), bcmul($prize[0], $buttonA[1])), $det);

    $minTokens = null;
    $searchRange = 1000;

    for ($k = -$searchRange; $k <= $searchRange; $k++) {
        $a = bcadd($countA, bcmul($buttonB[0], (string)$k));
        $b = bcadd($countB, bcmul($buttonA[0], (string)$k));

        $xCheck = bcadd(bcmul($a, $buttonA[0]), bcmul($b, $buttonB[0]));
        $yCheck = bcadd(bcmul($a, $buttonA[1]), bcmul($b, $buttonB[1]));

        if ($xCheck == $prize[0] && $yCheck == $prize[1]) {
            $currentTokens = bcadd(bcmul($a, '3'), $b);

            if ($minTokens === null || bccomp($currentTokens, $minTokens) < 0) {
                $minTokens = $currentTokens;
            }
        }
    }

    return $minTokens;
}

function solvePart2($input): string
{
    $machines = parseInput($input, true);
    $totalTokens = '0';

    foreach ($machines as $machine) {
        $minTokens = findMinimumTokens($machine);
        if ($minTokens !== null) {
            $totalTokens = bcadd($totalTokens, $minTokens);
        }
    }

    return $totalTokens;
}

function solvePart1($input): string
{
    $machines = parseInput($input);
    $totalTokens = '0';

    foreach ($machines as $machine) {
        $minTokens = findMinimumTokens($machine);
        if ($minTokens !== null) {
            $totalTokens = bcadd($totalTokens, $minTokens);
        }
    }

    return $totalTokens;
}

// Part 1
$profiler = new Profiler();
$profiler->startProfile();
$result1 = solvePart1($input);
$profiler->stopProfile();
echo "Fewest tokens to win possible prizes: {$result1}" . PHP_EOL;
$profiler->reportProfile();

// Part 2
$profiler = new Profiler();
$profiler->startProfile();
$result2 = solvePart2($input);
$profiler->stopProfile();
echo "Fewest tokens to win possible prizes: {$result2}" . PHP_EOL;
$profiler->reportProfile();