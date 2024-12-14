<?php
/**
 * Advent of Code 2024
 * Day 14: Restroom Redoubt
 *
 * @author Martin Diekhoff
 * @link https://adventofcode.com/2024/day/14
 */

const DATA_INPUT_FILE = 'input14.txt';

require_once __DIR__ . '/../' . 'bootstrap.php';

$input = DataImporter::importFromFileWithDefaultFlags(__DIR__ . '/' . DATA_INPUT_FILE);

const WIDTH = 101;
const HEIGHT = 103;
const TIME = 100;

function simulateRobotMovement($robots)
{
    for ($t = 0; $t < TIME; $t++) {
        foreach ($robots as &$robot) {
            $x = $robot[0];
            $y = $robot[1];
            $vx = $robot[2];
            $vy = $robot[3];

            $x += $vx;
            $y += $vy;

            // Precise wraparound handling
            $x = (($x % WIDTH) + WIDTH) % WIDTH;
            $y = (($y % HEIGHT) + HEIGHT) % HEIGHT;

            $robot[0] = $x;
            $robot[1] = $y;
        }
    }
    return $robots;
}

function getQuadrantsSafetyFactorPart1(): float|int
{
    // Define the width and height of the space
    $width = WIDTH;
    $height = HEIGHT;

    // Define the time to simulate
    $time = TIME;

    // Initialize an array to store the robot positions
    $robots = [];

    // Parse the input to get the robot positions and velocities
    foreach ($GLOBALS['input'] as $line) {
        [$position, $velocity] = explode(' ', $line);
        [$x, $y] = explode(',', substr($position, 2));
        [$vx, $vy] = explode(',', substr($velocity, 2));
        $robots[] = [(int)$x, (int)$y, (int)$vx, (int)$vy];
    }

    // Simulate the motion of the robots
    $robots = simulateRobotMovement($robots);

    // Initialize a 2D array to store the final positions
    $grid = array_fill(0, $height, array_fill(0, $width, 0));

    // Count the number of robots in each position
    foreach ($robots as $robot) {
        $x = $robot[0];
        $y = $robot[1];
        if ($x >= 0 && $x < $width && $y >= 0 && $y < $height) {
            $grid[$y][$x]++;
        }
    }

    // Print the final positions
//    echo "Final positions:\n";
//    foreach ($grid as $y => $row) {
//        $line = '';
//        foreach ($row as $x => $count) {
//            if ($x == floor($width / 2) || $y == floor($height / 2)) {
//                $line .= ' ';
//            } elseif ($count == 0) {
//                $line .= '.';
//            } else {
//                $line .= $count;
//            }
//        }
//        echo "$line\n";
//    }

    // Initialize an array to store the quadrant counts
    $quadrants = [0, 0, 0, 0];

    // Count the number of robots in each quadrant
    foreach ($grid as $y => $row) {
        foreach ($row as $x => $count) {
            if ($x == floor($width / 2) || $y == floor($height / 2)) {
                continue;
            }
            if ($x < floor($width / 2) && $y < floor($height / 2)) {
                $quadrants[0] += $count;
            } elseif ($x >= floor($width / 2) && $y < floor($height / 2)) {
                $quadrants[1] += $count;
            } elseif ($x < floor($width / 2) && $y >= floor($height / 2)) {
                $quadrants[2] += $count;
            } elseif ($x >= floor($width / 2) && $y >= floor($height / 2)) {
                $quadrants[3] += $count;
            }
        }
    }

    // Return the calculated safety factor
    return $quadrants[0] * $quadrants[1] * $quadrants[2] * $quadrants[3];
}

// Part 1

$profiler = new Profiler();
$profiler->startProfile();
$result1 = getQuadrantsSafetyFactorPart1();
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