<?php

$lines = file('input06.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

function simulateGuardMovement($lines): int
{
    $directions = [[0, -1], [1, 0], [0, 1], [-1, 0]]; // up, right, down, left
    $currentDirection = 0; // start facing up
    $currentPosition = null;

    // find the guard's starting position
    foreach ($lines as $y => $row) {
        foreach (str_split($row) as $x => $cell) { // Split the row into individual characters
            if ($cell == '^') {
                $currentPosition = [$x, $y];
                break 2;
            }
        }
    }

    $visitedPositions = []; // mark the visited positions
    $turns = []; // mark the positions and directions where the guard turned right
    $uniqueVisitedPositions = []; // keep track of unique visited positions
    $steps = 1; // Include the initial position in the step count

    echo "Starting position: (" . $currentPosition[0] . ", " . $currentPosition[1] . ")\n";

    // Add the initial position to the unique visited positions
    $uniqueVisitedPositions[implode(',', $currentPosition)] = true;

    while (true) {
        $nextPosition = [$currentPosition[0] + $directions[$currentDirection][0], $currentPosition[1] + $directions[$currentDirection][1]];

        // check if next position is within the map boundaries
        if ($nextPosition[0] < 0 || $nextPosition[0] >= strlen($lines[0]) || $nextPosition[1] < 0 || $nextPosition[1] >= count($lines)) {
            echo "Guard has left the grid at position: (" . $nextPosition[0] . ", " . $nextPosition[1] . ")\n";
            break; // guard has left the mapped area
        }

        // check if next position is an obstacle
        if ($lines[$nextPosition[1]][$nextPosition[0]] == '#') {
            echo "Obstacle in front at position: (" . $nextPosition[0] . ", " . $nextPosition[1] . "). Turning right.\n";
            // turn right
            $currentDirection = ($currentDirection + 1) % 4;
            $nextPosition = [$currentPosition[0] + $directions[$currentDirection][0], $currentPosition[1] + $directions[$currentDirection][1]];
        }

        // move to next position
        $currentPosition = $nextPosition;

        // add current position and direction to visited positions if it hasn't already been added
        $positionKey = implode(',', $currentPosition) . ',' . $currentDirection;
        if (!isset($visitedPositions[$positionKey]) && !isset($turns[implode(',', $currentPosition)])) {
            $visitedPositions[$positionKey] = true;
            $uniqueVisitedPositions[implode(',', $currentPosition)] = true;
            $steps++;
            echo "Moved to position: (" . $currentPosition[0] . ", " . $currentPosition[1] . ")\n";
        } else {
            echo "Position (" . $currentPosition[0] . ", " . $currentPosition[1] . ") has already been visited.\n";
        }
    }

    echo "Total steps taken: " . $steps . "\n";
    echo "Total unique positions visited: " . count($uniqueVisitedPositions) . "\n";

    return count($uniqueVisitedPositions);
}

echo PHP_EOL . "Distinct map positions occupied (Part 1): " . simulateGuardMovement($lines) . PHP_EOL;

// Part 2

