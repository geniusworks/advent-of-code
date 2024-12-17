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

function depthFirstSearch($grid, $i, $j, $plantType, &$visited, &$region): void
{
    $rows = count($grid);
    $cols = count($grid[0]);

    // Check bounds, visited status, and plant type
    if ($i < 0 || $i >= $rows || $j < 0 || $j >= $cols ||
        $visited[$i][$j] || $grid[$i][$j] !== $plantType) {
        return;
    }

    // Mark as visited and add to region
    $visited[$i][$j] = true;
    $region[] = [$i, $j];

    // Explore adjacent cells
    $directions = [[-1, 0], [1, 0], [0, -1], [0, 1]];
    foreach ($directions as $dir) {
        depthFirstSearch($grid, $i + $dir[0], $j + $dir[1], $plantType, $visited, $region);
    }
}

function calculatePerimeterPart1($region, $grid): int
{
    $perimeter = 0;
    $rows = count($grid);
    $cols = count($grid[0]);

    foreach ($region as $cell) {
        $i = $cell[0];
        $j = $cell[1];
        $plantType = $grid[$i][$j];
        $adjacentSides = 0;

        // Check 4 adjacent directions
        $directions = [[-1, 0], [1, 0], [0, -1], [0, 1]];
        foreach ($directions as $dir) {
            $ni = $i + $dir[0];
            $nj = $j + $dir[1];

            // Check if adjacent cell is within bounds and of same plant type
            if ($ni >= 0 && $ni < $rows && $nj >= 0 && $nj < $cols &&
                $grid[$ni][$nj] === $plantType) {
                $adjacentSides++;
            }
        }

        $perimeter += 4 - $adjacentSides;
    }

    return $perimeter;
}

function getTotalFencingPricePart1($input): int {
    $grid = array_map('str_split', $input);
    $regions = findRegions($grid);
    $totalPrice = 0;

    foreach ($regions as $region) {
        $area = count($region['cells']);
        $perimeter = calculatePerimeterPart1($region['cells'], $grid);
        $totalPrice += $area * $perimeter;
    }

    return $totalPrice;
}

function countSides($region, $grid, $regionMarker): int
{
    $sides = 0;
    $rows = count($grid);
    $cols = count($grid[0]);

    // Create an empty grid that is 1 cell bigger than the region on all four sides
    $emptyGrid = array_fill(0, $rows + 2, array_fill(0, $cols + 2, '.'));

    // Overlay the region on top of the empty grid
    foreach ($region as $cell) {
        $i = $cell[0] + 1;
        $j = $cell[1] + 1;
        $emptyGrid[$i][$j] = $regionMarker;
    }

    // Count segments of adjoined cells with empty space above them
    $currentSegment = 0;
    for ($i = 1; $i <= $rows; $i++) {
        for ($j = 1; $j <= $cols; $j++) {
            if ($emptyGrid[$i][$j] === $regionMarker && $emptyGrid[$i - 1][$j] === '.') {
                $currentSegment++;
            } elseif ($currentSegment > 0 && ($emptyGrid[$i][$j] !== $regionMarker || $emptyGrid[$i - 1][$j] !== '.')) {
                $sides++;
                $currentSegment = 0;
            }
        }
        if ($currentSegment > 0) {
            $sides++;
            $currentSegment = 0;
        }
    }

    // Count segments of adjoined cells with empty space below them
    $currentSegment = 0;
    for ($i = $rows; $i >= 1; $i--) {
        for ($j = 1; $j <= $cols; $j++) {
            if ($emptyGrid[$i][$j] === $regionMarker && $emptyGrid[$i + 1][$j] === '.') {
                $currentSegment++;
            } elseif ($currentSegment > 0 && ($emptyGrid[$i][$j] !== $regionMarker || $emptyGrid[$i + 1][$j] !== '.')) {
                $sides++;
                $currentSegment = 0;
            }
        }
        if ($currentSegment > 0) {
            $sides++;
            $currentSegment = 0;
        }
    }

    // Count segments of adjoined cells with empty space to the left of them
    $currentSegment = 0;
    for ($j = 1; $j <= $cols; $j++) {
        for ($i = 1; $i <= $rows; $i++) {
            if ($emptyGrid[$i][$j] === $regionMarker && $emptyGrid[$i][$j - 1] === '.') {
                $currentSegment++;
            } elseif ($currentSegment > 0 && ($emptyGrid[$i][$j] !== $regionMarker || $emptyGrid[$i][$j - 1] !== '.')) {
                $sides++;
                $currentSegment = 0;
            }
        }
        if ($currentSegment > 0) {
            $sides++;
            $currentSegment = 0;
        }
    }

    // Count segments of adjoined cells with empty space to the right of them
    $currentSegment = 0;
    for ($j = $cols; $j >= 1; $j--) {
        for ($i = 1; $i <= $rows; $i++) {
            if ($emptyGrid[$i][$j] === $regionMarker && $emptyGrid[$i][$j + 1] === '.') {
                $currentSegment++;
            } elseif ($currentSegment > 0 && ($emptyGrid[$i][$j] !== $regionMarker || $emptyGrid[$i][$j + 1] !== '.')) {
                $sides++;
                $currentSegment = 0;
            }
        }
        if ($currentSegment > 0) {
            $sides++;
            $currentSegment = 0;
        }
    }

    return $sides;
}

function findRegions($grid): array
{
    $rows = count($grid);
    $cols = count($grid[0]);
    $visited = array_fill(0, $rows, array_fill(0, $cols, false));
    $regions = [];
    $regionMarker = 1;

    for ($i = 0; $i < $rows; $i++) {
        for ($j = 0; $j < $cols; $j++) {
            if (!$visited[$i][$j] && $grid[$i][$j] !== '.') {
                $region = [];
                $plantType = $grid[$i][$j];
                depthFirstSearch($grid, $i, $j, $plantType, $visited, $region);
                $regions[] = [
                    'type' => $plantType,
                    'cells' => $region,
                    'marker' => $regionMarker
                ];
                $regionMarker++;
            }
        }
    }

    return $regions;
}

function getTotalFencingPricePart2($input): int {
    $grid = array_map('str_split', $input);
    $regions = findRegions($grid);
    $totalPrice = 0;

    foreach ($regions as $region) {
        $area = count($region['cells']);
        $sides = countSides($region['cells'], $grid, $region['marker']);

        // Optional debug print
        // echo "Region type {$region['type']}: area = $area, sides = $sides, price = " . ($area * $sides) . "\n";

        $totalPrice += $area * $sides;
    }

    return $totalPrice;
}

// Part 1
$profiler = new Profiler();
$profiler->startProfile();
$result1 = getTotalFencingPricePart1($input);
$profiler->stopProfile();
echo "Regular price of fencing all regions on the map: {$result1}" . PHP_EOL;
$profiler->reportProfile();

// Part 2
$profiler = new Profiler();
$profiler->startProfile();
$result2 = getTotalFencingPricePart2($input);
$profiler->stopProfile();
echo "Discount price of fencing all regions on the map: {$result2}" . PHP_EOL;
$profiler->reportProfile();