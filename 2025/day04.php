<?php
/**
 * Advent of Code 2025
 * Day 4: Printing Department
 *
 * @author Martin Diekhoff
 * @link https://adventofcode.com/2025/day/4
 */

const DATA_INPUT_FILE = 'input04.txt';

require_once __DIR__ . '/../bootstrap.php';

$input = DataImporter::importFromFileWithDefaultFlags(__DIR__ . '/' . DATA_INPUT_FILE);

function solvePart1(array $input)
{
    $grid = [];

    foreach ($input as $line) {
        $line = rtrim($line, "\r\n");
        if ($line === '') {
            continue;
        }
        $grid[] = str_split($line);
    }

    $rows = count($grid);
    if ($rows === 0) {
        return 0;
    }

    $cols = count($grid[0]);
    $accessible = 0;

    $dirs = [
        [-1, -1], [-1, 0], [-1, 1],
        [0, -1],           [0, 1],
        [1, -1],  [1, 0],  [1, 1],
    ];

    for ($r = 0; $r < $rows; $r++) {
        for ($c = 0; $c < $cols; $c++) {
            if ($grid[$r][$c] !== '@') {
                continue;
            }

            $adjacentRolls = 0;

            foreach ($dirs as [$dr, $dc]) {
                $nr = $r + $dr;
                $nc = $c + $dc;

                if ($nr < 0 || $nr >= $rows || $nc < 0 || $nc >= $cols) {
                    continue;
                }

                if ($grid[$nr][$nc] === '@') {
                    $adjacentRolls++;
                }
            }

            if ($adjacentRolls < 4) {
                $accessible++;
            }
        }
    }

    return $accessible;
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
