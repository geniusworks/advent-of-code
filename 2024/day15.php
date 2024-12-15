<?php
/**
 * Advent of Code 2024
 * Day 15:
 *
 * @author Martin Diekhoff
 * @link https://adventofcode.com/2024/day/15
 */

const DATA_INPUT_FILE = 'input15.txt';

require_once __DIR__ . '/../' . 'bootstrap.php';

$input = DataImporter::importFromFileContents(__DIR__ . '/' . DATA_INPUT_FILE);

function sumOfGPSCoordinates($input)
{
    $parts = explode("\n\n", $input);
    $map = explode("\n", $parts[0]);
    $moves = str_replace("\n", "", $parts[1]);

    // Find initial positions
    $robotX = $robotY = 0;
    $boxes = [];
    $maxX = strlen($map[0]) - 1;
    $maxY = count($map) - 1;

    for ($y = 0; $y < count($map); $y++) {
        for ($x = 0; $x < strlen($map[$y]); $x++) {
            if ($map[$y][$x] == '@') {
                $robotX = $x;
                $robotY = $y;
            } elseif ($map[$y][$x] == 'O') {
                $boxes[] = [$x, $y];
            }
        }
    }

    // Move directions
    $directions = [
        '^' => [0, -1],
        'v' => [0, 1],
        '<' => [-1, 0],
        '>' => [1, 0],
    ];

    // Process each move
    foreach (str_split($moves) as $move) {
        $dx = $directions[$move][0];
        $dy = $directions[$move][1];

        $newRobotX = $robotX + $dx;
        $newRobotY = $robotY + $dy;

        // Check if move is blocked by wall
        if ($map[$newRobotY][$newRobotX] == '#') {
            continue;
        }

        // Check if move involves boxes
        $boxesToMove = [];
        $currentX = $newRobotX;
        $currentY = $newRobotY;

        // Find boxes in the path
        while (true) {
            $boxFound = false;
            foreach ($boxes as $index => $box) {
                if ($box[0] == $currentX && $box[1] == $currentY) {
                    $boxFound = true;
                    $boxesToMove[] = $index;
                    $currentX += $dx;
                    $currentY += $dy;
                    break;
                }
            }

            // If no box found or next position is a wall, stop checking
            if (!$boxFound || $map[$currentY][$currentX] == '#') {
                break;
            }
        }

        // If boxes block the way and can't be pushed, skip
        if ($map[$currentY][$currentX] == '#') {
            continue;
        }

        // Move boxes
        foreach (array_reverse($boxesToMove) as $index) {
            $boxes[$index][0] += $dx;
            $boxes[$index][1] += $dy;
        }

        // Move robot
        $robotX = $newRobotX;
        $robotY = $newRobotY;
    }

    // Calculate the sum of GPS coordinates
    $sum = 0;
    foreach ($boxes as $box) {
        $sum += 100 * $box[1] + $box[0];
    }

    return $sum;
}

// Part 1

$profiler = new Profiler();
$profiler->startProfile();
$result1 = sumOfGPSCoordinates($input);
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