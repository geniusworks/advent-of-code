<?php
/**
 * Advent of Code 2024
 * Day 18: RAM Run
 *
 * @author Martin Diekhoff
 * @link https://adventofcode.com/2024/day/18
 */

const DATA_INPUT_FILE = 'input18.txt';

require_once __DIR__ . '/../' . 'bootstrap.php';

$input = DataImporter::importFromFileWithDefaultFlags(__DIR__ . '/' . DATA_INPUT_FILE);

function getMinimumStepsToExit($input, $maxSteps = 1024) {
    // If input is a grid string, convert it to a 2D array
    if (is_string($input)) {
        $lines = explode("\n", trim($input));
        $memorySpace = array_map('str_split', $lines);
        $maxX = count($memorySpace[0]) - 1;
        $maxY = count($memorySpace) - 1;
    } else {
        // Determine grid size dynamically from coordinate input
        $maxX = max(array_map(function ($value) {
            list($x, $y) = explode(',', $value);
            return $x;
        }, $input));
        $maxY = max(array_map(function ($value) {
            list($x, $y) = explode(',', $value);
            return $y;
        }, $input));

        // Initialize memory space
        $memorySpace = array_fill(0, $maxX + 1, array_fill(0, $maxY + 1, '.'));

        // Mark corrupted bytes
        $byteCount = 0;
        foreach ($input as $bytePosition) {
            list($x, $y) = explode(',', $bytePosition);
            $memorySpace[$x][$y] = '#';
            $byteCount++;
            if ($byteCount >= $maxSteps) {
                break;
            }
        }
    }

    // Define possible movements (up, down, left, right)
    $movements = [[-1, 0], [1, 0], [0, -1], [0, 1]];

    // Create a queue for BFS
    $queue = new SplQueue();
    $queue->enqueue([0, 0, 0]); // [x, y, steps]

    // Track visited states to prevent redundant exploration
    $visited = array_fill(0, count($memorySpace[0]), array_fill(0, count($memorySpace), PHP_INT_MAX));
    $visited[0][0] = 0;

    while (!$queue->isEmpty()) {
        list($x, $y, $steps) = $queue->dequeue();

        // Check if we've reached the exit
        if ($x === count($memorySpace[0]) - 1 && $y === count($memorySpace) - 1) {
            return $steps;
        }

        // Explore neighboring positions
        foreach ($movements as $movement) {
            $newX = $x + $movement[0];
            $newY = $y + $movement[1];

            // Check boundaries and non-corrupted spaces
            if ($newX >= 0 && $newX < count($memorySpace[0]) &&
                $newY >= 0 && $newY < count($memorySpace) &&
                $memorySpace[$newY][$newX] === '.' &&
                $steps + 1 < $visited[$newX][$newY]) {

                $queue->enqueue([$newX, $newY, $steps + 1]);
                $visited[$newX][$newY] = $steps + 1;
            }
        }
    }

    // If no path is found
    return -1;
}

function printGrid($grid) {
    $transposedGrid = array_map(null, ...$grid);
    foreach ($transposedGrid as $row) {
        echo implode('', $row) . PHP_EOL;
    }
}

// Part 1

$profiler = new Profiler();
$profiler->startProfile();
$result1 = getMinimumStepsToExit($input);
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