<?php

/**
 * Advent of Code 2024
 * Day 21: Race Condition
 *
 * @author Martin Diekhoff
 * @link https://adventofcode.com/2024/day/21
 */

const DATA_INPUT_FILE = 'input21.txt';
const DIRECTIONS = ["^" => [-1, 0], ">" => [0, 1], "v" => [1, 0], "<" => [0, -1]];

require_once __DIR__ . '/../' . 'bootstrap.php';

$input = DataImporter::importFromFileWithDefaultFlags(__DIR__ . '/' . DATA_INPUT_FILE);

function solvePart1($input): float|int
{
    $pads = [
        [["789", "456", "123", " 0A"], []],
        [[" ^A", "<v>"], []],
    ];

    $pads[0][1] = createKeyMap($pads[0][0]);
    $pads[1][1] = createKeyMap($pads[1][0]);

    return array_sum(array_map(fn($line) => getOptimalPath($line, 2, $pads) * (int)$line, $input));
}

function createKeyMap($grid): array
{
    $keyMap = [];
    foreach ($grid as $rowIndex => $row) {
        foreach (str_split($row) as $columnIndex => $value) {
            if ($value !== " ") {
                $keyMap[$value] = [$rowIndex, $columnIndex];
            }
        }
    }
    return $keyMap;
}

function search($start, $end, $pads, $padType = 0): array
{
    [$grid, $gridKeyMap] = $pads[$padType];
    $rowCount = count($grid);
    $columnCount = strlen($grid[0]);
    $queue = [[$start, 0, ""]];
    $visited = [$start => 0];
    $permutations = [];
    while ($queue) {
        [$current, $distance, $path] = array_shift($queue);
        [$row, $column] = $gridKeyMap[$current];
        if ($current === $end) {
            $permutations[] = $path . "A";
            continue;
        }
        foreach (DIRECTIONS as $direction => $delta) {
            [$deltaRow, $deltaColumn] = $delta;
            $newRow = $row + $deltaRow;
            $newColumn = $column + $deltaColumn;
            if ($newRow < 0 || $newRow >= $rowCount || $newColumn < 0 || $newColumn >= $columnCount) {
                continue;
            }
            if (($next = $grid[$newRow][$newColumn]) === " ") {
                continue;
            }
            if (isset($visited[$next]) && $visited[$next] < $distance + 1) {
                continue;
            }
            $visited[$next] = $distance + 1;
            $queue[] = [$next, $distance + 1, $path . $direction];
        }
    }
    return $permutations;
}

function getOptimalPath($code, $depth, $pads, $padType = 0)
{
    static $cache = [];
    if (isset($cache[$key = "$code,$depth"])) {
        return $cache[$key];
    }
    $result = 0;
    for ($i = 0; $i < strlen($code); $i++) {
        $permutations = search($code[$i - 1] ?? "A", $code[$i], $pads, $padType);
        $result += $depth == 0 ? min(array_map("strlen", $permutations)) : min(
            array_map(fn($p) => getOptimalPath($p, $depth - 1, $pads, 1), $permutations),
        );
    }
    return $cache[$key] = $result;
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
echo "Sum of the complexities of the five listed codes: {$result1}" . PHP_EOL;
$profiler->reportProfile();

// Part 2

$profiler = new Profiler();
$profiler->startProfile();
$result2 = solvePart2($input); // TODO: Calculate the result for part 2.
$profiler->stopProfile();
echo "Result: {$result2}" . PHP_EOL;
$profiler->reportProfile();