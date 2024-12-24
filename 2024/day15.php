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
    $gpsSum = 0;
    foreach ($boxes as $box) {
        $gpsSum += 100 * $box[1] + $box[0];
    }

    return $gpsSum;
}

function sumOfGPSCoordinatesWide($input): float|int|array
{
    $parts = explode("\n\n", $input);
    $map = explode("\n", $parts[0]);
    $moves = str_replace("\n", "", $parts[1]);

    // Find initial robot position
    $robotX = $robotY = 0;
    for ($y = 0; $y < count($map); $y++) {
        if (str_contains($map[$y], "@")) {
            [$robotY, $robotX] = [$y, strpos($map[$y], "@")];
            break;
        }
    }

    $directions = [
        '^' => [-1, 0],
        '>' => [0, 1],
        'v' => [1, 0],
        '<' => [0, -1],
    ];

    function processMap($map, $startPosition, $moves, $directions): float|int
    {
        $maxRows = count($map);
        $maxCols = strlen($map[0]);
        [$currentY, $currentX] = $startPosition;

        foreach (str_split($moves) as $move) {
            [$dy, $dx] = $directions[$move];
            [$newY, $newX] = [$currentY + $dy, $currentX + $dx];

            switch ($map[$newY][$newX]) {
                case '#':
                    continue 2;
                case '.':
                case '@':
                    [$currentY, $currentX] = [$newY, $newX];
                    continue 2;
                case 'O':
                case '[':
                case ']':
                    $positionsToCheck = [[$currentY, $currentX]];
                    $checkedPositions = [];

                    while ($positionsToCheck) {
                        [$checkY, $checkX] = array_shift($positionsToCheck);
                        $positionKey = "$checkY,$checkX";

                        if (isset($checkedPositions[$positionKey])) {
                            continue;
                        }

                        $checkedPositions[$positionKey] = 1;
                        [$nextY, $nextX] = [$checkY + $dy, $checkX + $dx];
                        $nextTile = $map[$nextY][$nextX];

                        switch ($nextTile) {
                            case '#':
                                continue 4;
                            case 'O':
                                $positionsToCheck[] = [$nextY, $nextX];
                                break;
                            case '[':
                            case ']':
                                $positionsToCheck[] = [$nextY, $nextX];
                                $positionsToCheck[] = [$nextY, $nextX + ($nextTile == '[' ? 1 : -1)];
                        }
                    }

                    // Move boxes to their new positions
                    while ($checkedPositions) {
                        foreach (array_keys($checkedPositions) as $positionKey) {
                            [$checkY, $checkX] = explode(',', $positionKey);
                            [$nextY, $nextX] = [$checkY + $dy, $checkX + $dx];

                            if (!isset($checkedPositions["$nextY,$nextX"])) {
                                $map[$nextY][$nextX] = $map[$checkY][$checkX];
                                $map[$checkY][$checkX] = '.';
                                unset($checkedPositions["$checkY,$checkX"]);
                            }
                        }
                    }
                    [$currentY, $currentX] = [$currentY + $dy, $currentX + $dx];
            }
        }

        // Calculate GPS coordinates
        $gpsSum = 0;
        for ($y = 0; $y < $maxRows; $y++) {
            for ($x = 0; $x < $maxCols; $x++) {
                if (in_array($map[$y][$x], ['[', 'O'])) {
                    $gpsSum += 100 * $y + $x;
                }
            }
        }
        return $gpsSum;
    }

    processMap($map, [$robotY, $robotX], $moves, $directions);

    // Expand map for wide mode
    for ($i = 0; $i < count($map); $i++) {
        $row = $map[$i];
        $row = str_replace(
            ['#', 'O', '.', '@'],
            ['##', '[]', '..', '@.'],
            $row,
        );
        $map[$i] = $row;
    }

    return processMap($map, [$robotY, $robotX * 2], $moves, $directions);
}

// Part 1

$profiler = new Profiler();
$profiler->startProfile();
$result1 = sumOfGPSCoordinates($input);
$profiler->stopProfile();
echo "Sum of GPS Coordinates: {$result1}" . PHP_EOL;
$profiler->reportProfile();

// Part 2

$profiler = new Profiler();
$profiler->startProfile();
$result2 = "Sum of GPS Coordinates (wide): " . sumOfGPSCoordinatesWide($input);
$profiler->stopProfile();
echo "Result: {$result2}" . PHP_EOL;
$profiler->reportProfile();