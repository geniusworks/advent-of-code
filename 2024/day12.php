<?php
/**
 * Advent of Code 2024
 * Day 12: Garden Groups
 *
 * @author Martin Diekhoff
 * @link https://adventofcode.com/2024/day/12
 */

const DATA_INPUT_FILE = 'input12.txt';

require_once __DIR__ . '/../' . 'bootstrap.php';

$input = DataImporter::importFromFileWithDefaultFlags(__DIR__ . '/' . DATA_INPUT_FILE);

$grid = array_map('str_split', $input);

$visited = array_fill(0, count($grid), array_fill(0, count($grid[0]), false));
$regions = [];

function dfs($i, $j, $plantType, &$visited, &$regions, &$grid)
{
    if ($i < 0 || $i >= count($grid) || $j < 0 || $j >= count($grid[0])
        || $visited[$i][$j] || $grid[$i][$j] !== $plantType) {
        return;
    }

    $visited[$i][$j] = true;
    $region = &$regions[$plantType];
    if (!isset($region)) {
        $region = [];
    }
    $region[] = [$i, $j];

    dfs($i - 1, $j, $plantType, $visited, $regions, $grid);
    dfs($i + 1, $j, $plantType, $visited, $regions, $grid);
    dfs($i, $j - 1, $plantType, $visited, $regions, $grid);
    dfs($i, $j + 1, $plantType, $visited, $regions, $grid);
}

for ($i = 0; $i < count($grid); $i++) {
    for ($j = 0; $j < count($grid[0]); $j++) {
        if (!$visited[$i][$j]) {
            dfs($i, $j, $grid[$i][$j], $visited, $regions, $grid);
        }
    }
}

$totalPrice = 0;
foreach ($regions as $regionLetter => $region) {
    $separateRegions = [];
    foreach ($region as $cell) {
        $found = false;
        foreach ($separateRegions as $separateRegion) {
            if (in_array($cell, $separateRegion)) {
                $found = true;
                break;
            }
        }
        if (!$found) {
            $separateRegion = [$cell];
            $stack = [$cell];
            while (!empty($stack)) {
                $currentCell = array_pop($stack);
                $i = $currentCell[0];
                $j = $currentCell[1];
                if ($i > 0 && $grid[$i - 1][$j] === $grid[$i][$j] && !in_array([$i - 1, $j], $separateRegion)) {
                    $separateRegion[] = [$i - 1, $j];
                    $stack[] = [$i - 1, $j];
                }
                if ($i < count($grid) - 1 && $grid[$i + 1][$j] === $grid[$i][$j] && !in_array([$i + 1, $j],
                        $separateRegion)) {
                    $separateRegion[] = [$i + 1, $j];
                    $stack[] = [$i + 1, $j];
                }
                if ($j > 0 && $grid[$i][$j - 1] === $grid[$i][$j] && !in_array([$i, $j - 1], $separateRegion)) {
                    $separateRegion[] = [$i, $j - 1];
                    $stack[] = [$i, $j - 1];
                }
                if ($j < count($grid[0]) - 1 && $grid[$i][$j + 1] === $grid[$i][$j] && !in_array([$i, $j + 1],
                        $separateRegion)) {
                    $separateRegion[] = [$i, $j + 1];
                    $stack[] = [$i, $j + 1];
                }
            }
            $separateRegions[] = $separateRegion;
        }
    }
    foreach ($separateRegions as $separateRegion) {
        $area = count($separateRegion);
        $perimeter = 0;
        foreach ($separateRegion as $cell) {
            $i = $cell[0];
            $j = $cell[1];
            $sharedEdges = 0;
            if ($i > 0 && $grid[$i - 1][$j] === $grid[$i][$j]) {
                $sharedEdges++;
            }
            if ($i < count($grid) - 1 && $grid[$i + 1][$j] === $grid[$i][$j]) {
                $sharedEdges++;
            }
            if ($j > 0 && $grid[$i][$j - 1] === $grid[$i][$j]) {
                $sharedEdges++;
            }
            if ($j < count($grid[0]) - 1 && $grid[$i][$j + 1] === $grid[$i][$j]) {
                $sharedEdges++;
            }
            $perimeter += 4 - $sharedEdges;
        }
        echo "Region {$regionLetter} has area {$area} and perimeter {$perimeter}" . PHP_EOL;
        $totalPrice += $area * $perimeter;
    }
}

// Part 1

$profiler = new Profiler();
$profiler->startProfile();
$result1 = $totalPrice;
$profiler->stopProfile();
echo "Price of fencing all regions on the map: {$result1}" . PHP_EOL;
$profiler->reportProfile();

// Part 2

$profiler = new Profiler();
$profiler->startProfile();
$result2 = $totalPrice; // TODO: Calculate result for part 2.
$profiler->stopProfile();
echo "Result = {$result2}" . PHP_EOL;
$profiler->reportProfile();