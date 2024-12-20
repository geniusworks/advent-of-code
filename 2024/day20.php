<?php
/**
 * Advent of Code 2024
 * Day 20: Race Condition
 *
 * @author Martin Diekhoff
 * @link https://adventofcode.com/2024/day/20
 */

const DATA_INPUT_FILE = 'input20.txt';

require_once __DIR__ . '/../' . 'bootstrap.php';

$input = DataImporter::importFromFileWithDefaultFlags(__DIR__ . '/' . DATA_INPUT_FILE);

const MINIMUM_PICOSECONDS_SAVED = 100;

function solvePart1($input): float|int
{
    $grid = array_map('str_split', $input);
    $rows = count($grid);
    $cols = count($grid[0]);

    // Find start and end positions
    $start = $end = null;
    for ($i = 0; $i < $rows; $i++) {
        for ($j = 0; $j < $cols; $j++) {
            if ($grid[$i][$j] === 'S') {
                $start = [$i, $j];
                $grid[$i][$j] = '.';
            } elseif ($grid[$i][$j] === 'E') {
                $end = [$i, $j];
                $grid[$i][$j] = '.';
            }
        }
    }

    // Pre-calculate distances from start to all points and from all points to end
    $distancesFromStart = calculateAllDistances($grid, $start);
    $distancesToEnd = calculateAllDistances($grid, $end, true);

    // Normal shortest path length
    $normalDistance = $distancesFromStart["{$end[0]},{$end[1]}"];

    // Find cheats
    $cheats = [];
    $directions = [[-1, 0], [1, 0], [0, -1], [0, 1]];

    // Only try positions that are reachable from both start and end
    foreach ($distancesFromStart as $posStr => $distFromStart) {
        if (!isset($distancesToEnd[$posStr])) {
            continue;
        }

        [$row, $col] = explode(',', $posStr);
        $row = (int)$row;
        $col = (int)$col;

        // Try two-move sequences
        foreach ($directions as $dir1) {
            foreach ($directions as $dir2) {
                $pos1 = [$row + $dir1[0], $col + $dir1[1]];
                $pos2 = [$pos1[0] + $dir2[0], $pos1[1] + $dir2[1]];

                if (!isInBounds($pos2, $rows, $cols)) {
                    continue;
                }

                // Check if this is a valid cheat sequence
                if (isValidCheatSequence($grid, [$row, $col], $pos1, $pos2)) {
                    $pos2Str = "{$pos2[0]},{$pos2[1]}";
                    if (!isset($distancesToEnd[$pos2Str])) {
                        continue;
                    }

                    $totalDist = $distFromStart + 2 + $distancesToEnd[$pos2Str];
                    $timeSaved = $normalDistance - $totalDist;

                    if ($timeSaved > 0) {
                        $cheats[$timeSaved] = ($cheats[$timeSaved] ?? 0) + 1;
                    }
                }
            }
        }
    }

    // Debug output for verification
//    ksort($cheats);
//    foreach ($cheats as $saving => $count) {
//        echo "Saves $saving picoseconds: $count cheats\n";
//    }

    // Return count of cheats saving >= 100 picoseconds
    return array_sum(
        array_filter(
            $cheats,
            fn($saving) => $saving >= MINIMUM_PICOSECONDS_SAVED,
            ARRAY_FILTER_USE_KEY,
        ),
    );
}

function calculateAllDistances($grid, $start, $reverse = false): array
{
    $rows = count($grid);
    $cols = count($grid[0]);
    $distances = [];
    $queue = new SplQueue();

    $startStr = "{$start[0]},{$start[1]}";
    $queue->enqueue([$start[0], $start[1], 0]);
    $distances[$startStr] = 0;

    $directions = [[-1, 0], [1, 0], [0, -1], [0, 1]];

    while (!$queue->isEmpty()) {
        [$row, $col, $dist] = $queue->dequeue();

        foreach ($directions as [$dr, $dc]) {
            $newRow = $row + $dr;
            $newCol = $col + $dc;

            if (isInBounds([$newRow, $newCol], $rows, $cols) &&
                $grid[$newRow][$newCol] === '.') {
                $posStr = "{$newRow},{$newCol}";
                if (!isset($distances[$posStr])) {
                    $distances[$posStr] = $dist + 1;
                    $queue->enqueue([$newRow, $newCol, $dist + 1]);
                }
            }
        }
    }

    return $distances;
}

function isInBounds($pos, $rows, $cols): bool
{
    return $pos[0] >= 0 && $pos[0] < $rows && $pos[1] >= 0 && $pos[1] < $cols;
}

function isValidCheatSequence($grid, $start, $pos1, $pos2): bool
{
    if (!isInBounds($pos1, count($grid), count($grid[0])) ||
        !isInBounds($pos2, count($grid), count($grid[0]))) {
        return false;
    }

    // Quick bounds check already done
    $hasWall = ($grid[$pos1[0]][$pos1[1]] === '#' || $grid[$pos2[0]][$pos2[1]] === '#');
    $endsOnTrack = ($grid[$pos2[0]][$pos2[1]] === '.');

    return $hasWall && $endsOnTrack;
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
echo "Number of cheats saving you at least 100 picoseconds: {$result1}" . PHP_EOL;
$profiler->reportProfile();

// Part 2

$profiler = new Profiler();
$profiler->startProfile();
$result2 = solvePart2($input); // TODO: Calculate the result for part 2.
$profiler->stopProfile();
echo "Result: {$result2}" . PHP_EOL;
$profiler->reportProfile();