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

function initializeRobots($input): array
{
    $robots = [];

    // Parse the input to get the robot positions and velocities
    foreach ($input as $line) {
        [$position, $velocity] = explode(' ', $line);
        [$x, $y] = explode(',', substr($position, 2));
        [$vx, $vy] = explode(',', substr($velocity, 2));
        $robots[] = [(int)$x, (int)$y, (int)$vx, (int)$vy];
    }

    return $robots;
}

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

function getQuadrantsSafetyFactor($robots): float|int
{
    // Simulate the motion of the robots
    $robots = simulateRobotMovement($robots);

    // Initialize a 2D array to store the final positions
    $grid = array_fill(0, HEIGHT, array_fill(0, WIDTH, 0));

    // Count the number of robots in each position
    foreach ($robots as $robot) {
        $x = $robot[0];
        $y = $robot[1];
        if ($x >= 0 && $x < WIDTH && $y >= 0 && $y < HEIGHT) {
            $grid[$y][$x]++;
        }
    }

    // Initialize an array to store the quadrant counts
    $quadrants = [0, 0, 0, 0];

    // Count the number of robots in each quadrant
    foreach ($grid as $y => $row) {
        foreach ($row as $x => $count) {
            if ($x == floor(WIDTH / 2) || $y == floor(HEIGHT / 2)) {
                continue;
            }
            if ($x < floor(WIDTH / 2) && $y < floor(HEIGHT / 2)) {
                $quadrants[0] += $count;
            } elseif ($x >= floor(WIDTH / 2) && $y < floor(HEIGHT / 2)) {
                $quadrants[1] += $count;
            } elseif ($x < floor(WIDTH / 2) && $y >= floor(HEIGHT / 2)) {
                $quadrants[2] += $count;
            } elseif ($x >= floor(WIDTH / 2) && $y >= floor(HEIGHT / 2)) {
                $quadrants[3] += $count;
            }
        }
    }

    // Return the calculated safety factor
    return $quadrants[0] * $quadrants[1] * $quadrants[2] * $quadrants[3];
}

function printGrid($grid): void
{
    foreach ($grid as $y => $row) {
        $line = '';
        foreach ($row as $x => $count) {
            if ($count == 0) {
                $line .= '.';
            } else {
                $line .= $count;
            }
        }
        echo "$line\n";
    }
    echo "\n";
}

function getRobotConvergenceSeconds(array &$robots): int
{
    $centerX = floor(WIDTH / 2);
    $centerY = floor(HEIGHT / 2);
    $maxRobotsInCenter = 0;
    $timeOfMaxRobots = 0;
    $gridAtMaxRobots = null;

    for ($t = 0; $t < 10000; $t++) {
        $robotsInCenter = 0;
        $grid = array_fill(0, HEIGHT, array_fill(0, WIDTH, 0));
        foreach ($robots as $robot) {
            $x = (($robot[0] + $robot[2] * $t) % WIDTH + WIDTH) % WIDTH;
            $y = (($robot[1] + $robot[3] * $t) % HEIGHT + HEIGHT) % HEIGHT;
            $grid[$y][$x]++;
            if (abs($x - $centerX) <= 1 && abs($y - $centerY) <= 1) {
                $robotsInCenter++;
            }
        }
        if ($robotsInCenter > $maxRobotsInCenter) {
            $maxRobotsInCenter = $robotsInCenter;
            $timeOfMaxRobots = $t;
            $gridAtMaxRobots = $grid;
        }
        if ($robotsInCenter >= count($robots) * 0.8) {
            return $t;
        }
    }

    if ($gridAtMaxRobots !== null) {
        printGrid($gridAtMaxRobots);
    }

    return $timeOfMaxRobots;
}

$robotArmy1 = initializeRobots($input);
$robotArmy2 = $robotArmy1;

// Part 1

$profiler = new Profiler();
$profiler->startProfile();
$result1 = getQuadrantsSafetyFactor($robotArmy1);
$profiler->stopProfile();
echo "Quadratic safety factor: {$result1}" . PHP_EOL;
$profiler->reportProfile();

// Part 2

$profiler = new Profiler();
$profiler->startProfile();
$result2 = getRobotConvergenceSeconds($robotArmy2, 0.2);
$profiler->stopProfile();
echo "Robot convergence seconds: {$result2}" . PHP_EOL;
$profiler->reportProfile();