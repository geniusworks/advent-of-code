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

    while (!empty($queue)) {
        [$position, $score, $currentDirection, $path] = array_shift($queue);

        // Create a unique key for visited tracking
        $key = implode(',', $position) . ',' . $currentDirection;

        // Skip if this state has been visited
        if (isset($visited[$key]) && $visited[$key] <= $score) {
            continue;
        }
        $visited[$key] = $score;

        // Check if we've reached the end
        if ($grid[$position[0]][$position[1]] === 'E') {
            return $score;
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

    return -1; // No path found
}

// Part 1

$profiler = new Profiler();
$profiler->startProfile();
$result1 = findLowestSolutionScore($input);
$profiler->stopProfile();
echo "Result: {$result1}" . PHP_EOL;
$profiler->reportProfile();

// Part 2

$profiler = new Profiler();
$profiler->startProfile();
$result2 = null; // TODO: Calculate the result for part 2.
$profiler->stopProfile();
echo "Result: {$result2}" . PHP_EOL;
$profiler->reportProfile();