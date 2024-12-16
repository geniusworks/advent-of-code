<?php
/**
 * Advent of Code 2024
 * Day 16: Reindeer Maze
 *
 * @author Martin Diekhoff
 * @link https://adventofcode.com/2024/day/16
 */

const DATA_INPUT_FILE = 'input16.txt';

require_once __DIR__ . '/../' . 'bootstrap.php';

$input = DataImporter::importFromFileWithDefaultFlags(__DIR__ . '/' . DATA_INPUT_FILE);

function findLowestSolutionScore($input): int
{
    $grid = array_map('str_split', $input);
    $rows = count($grid);
    $cols = count($grid[0]);
    $start = null;

    // Find the start position
    for ($i = 0; $i < $rows; $i++) {
        for ($j = 0; $j < $cols; $j++) {
            if ($grid[$i][$j] === 'S') {
                $start = [$i, $j];
                break 2;
            }
        }
    }

    // Ensure initial direction is East (index 3)
    $initialDirection = 3;

    // Define the possible movements (up, down, left, right)
    $directions = [[-1, 0], [1, 0], [0, -1], [0, 1]];

    // Priority queue stores: [position, score, current direction, path]
    $priorityQueue = new SplPriorityQueue();
    $priorityQueue->setExtractFlags(SplPriorityQueue::EXTR_DATA);
    $priorityQueue->insert([$start, 0, $initialDirection, []], 0);

    $visited = [];

    while (!$priorityQueue->isEmpty()) {
        [$position, $score, $currentDirection, $path] = $priorityQueue->extract();

        $key = implode(',', $position) . ',' . $currentDirection;

        if (isset($visited[$key]) && $visited[$key] <= $score) {
            continue;
        }
        $visited[$key] = $score;

        if ($grid[$position[0]][$position[1]] === 'E') {
            return $score;
        }

        for ($turn = 0; $turn < 4; $turn++) {
            $newDirection = ($currentDirection + $turn) % 4;
            $newPosition = [
                $position[0] + $directions[$newDirection][0],
                $position[1] + $directions[$newDirection][1],
            ];

            if ($newPosition[0] >= 0 && $newPosition[0] < $rows &&
                $newPosition[1] >= 0 && $newPosition[1] < $cols &&
                $grid[$newPosition[0]][$newPosition[1]] !== '#') {
                $newScore = $score + 1;
                if ($turn > 0) {
                    $newScore += 1000;
                }

                $priorityQueue->insert([$newPosition, $newScore, $newDirection, $path], -$newScore);
            }
        }
    }

    return -1;
}

function bestPathsTilesCount($input): int
{
    $grid = array_map('str_split', $input);
    $rows = count($grid);
    $cols = count($grid[0]);
    $start = null;

    // Find the start position
    for ($i = 0; $i < $rows; $i++) {
        for ($j = 0; $j < $cols; $j++) {
            if ($grid[$i][$j] === 'S') {
                $start = [$i, $j];
                break;
            }
        }
        if ($start) {
            break;
        }
    }

    // Ensure initial direction is East (index 3)
    $initialDirection = 3;

    // Define the possible movements (up, down, left, right)
    $directions = [[-1, 0], [1, 0], [0, -1], [0, 1]];

    // Queue stores: [position, score, current direction, path]
    $queue = [[$start, 0, $initialDirection, []]];
    $visited = [];
    $bestPaths = [];
    $lowestScore = PHP_INT_MAX;

    while (!empty($queue)) {
        [$position, $score, $currentDirection, $path] = array_shift($queue);

        // Create a unique key for visited tracking
        $key = implode(',', $position) . ',' . $currentDirection;

        // Prune paths that exceed current best score
        if ($lowestScore !== PHP_INT_MAX && $score > $lowestScore) {
            break;
        }

        // Skip if this state has been visited with a lower score
        if (isset($visited[$key]) && $visited[$key] < $score) {
            continue;
        }
        $visited[$key] = $score;

        // Check if we've reached the end
        if ($grid[$position[0]][$position[1]] === 'E') {
            // If this is the first path or matches the lowest score
            if ($score < $lowestScore) {
                $lowestScore = $score;
                $bestPaths = [array_merge($path, [$position])];
            } elseif ($score === $lowestScore) {
                $bestPaths[] = array_merge($path, [$position]);
            }
            continue;
        }

        // Try all possible moves (forward and turns)
        for ($turn = 0; $turn < 4; $turn++) {
            $newDirection = ($currentDirection + $turn) % 4;
            $newPosition = [
                $position[0] + $directions[$newDirection][0],
                $position[1] + $directions[$newDirection][1],
            ];

            // Validate new position
            if ($newPosition[0] >= 0 && $newPosition[0] < $rows &&
                $newPosition[1] >= 0 && $newPosition[1] < $cols &&
                $grid[$newPosition[0]][$newPosition[1]] !== '#') {
                // Calculate new score
                $newScore = $score + 1; // Base move cost
                if ($turn > 0) {
                    $newScore += 1000; // Turn cost
                }

                $newPath = array_merge($path, [$newPosition]);

                $queue[] = [$newPosition, $newScore, $newDirection, $newPath];
            }
        }

        // Sort queue to prioritize lower scores
        usort($queue, function ($a, $b) {
            return $a[1] <=> $b[1];
        });
    }

    // Count unique tiles on best paths and create visualization
    $bestPathTiles = [];
    $visualGrid = array_map(function ($row) {
        return array_map(function ($cell) {
            return $cell === '#' ? '#' : '.';
        }, $row);
    }, $grid);

    foreach ($bestPaths as $path) {
        foreach ($path as $tile) {
            $key = $tile[0] . ',' . $tile[1];
            // Only count non-wall tiles
            if ($grid[$tile[0]][$tile[1]] !== '#') {
                $bestPathTiles[$key] = true;
                // Mark tile as 'O' in visualization
                $visualGrid[$tile[0]][$tile[1]] = 'O';
            }
        }
    }

    // Add this block
    if ($grid[$start[0]][$start[1]] !== '#') {
        $key = $start[0] . ',' . $start[1];
        $bestPathTiles[$key] = true;
        // Ensure 'S' is marked in visualization
        $visualGrid[$start[0]][$start[1]] = 'O';
    }

    // Print the visualization
    echo "Visualization of best paths:\n";
    foreach ($visualGrid as $row) {
        echo implode('', $row) . "\n";
    }

    // Print the tile count
    $tileCount = count($bestPathTiles);
    echo "\nTotal unique tiles on best paths: $tileCount\n";

    return $tileCount;
}

// Part 1

$profiler = new Profiler();
$profiler->startProfile();
$result1 = findLowestSolutionScore($input);
$profiler->stopProfile();
echo "Lowest maze solution score: {$result1}" . PHP_EOL;
$profiler->reportProfile();

// Part 2

$profiler = new Profiler();
$profiler->startProfile();
$result2 = bestPathsTilesCount($input);
$profiler->stopProfile();
echo "Best paths tiles count: {$result2}" . PHP_EOL;
$profiler->reportProfile();