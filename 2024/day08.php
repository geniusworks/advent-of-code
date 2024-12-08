<?php
/**
 * Advent of Code 2024
 * Day 8: Resonant Collinearity
 *
 * @author Martin Diekhoff
 * @link https://adventofcode.com/2024/day/8
 */

class Antenna
{
    public $x;
    public $y;
    public $frequency;

    public function __construct($x, $y, $frequency)
    {
        $this->x = $x;
        $this->y = $y;
        $this->frequency = $frequency;
    }
}

function calculateImpact($input): int
{
    $start = microtime(true);
    $memory = memory_get_usage();

    $antennas = [];
    $antinodes = [];
    $map = $input;

    // Parse the map and create Antenna objects
    foreach ($input as $y => $row) {
        foreach (str_split($row) as $x => $cell) {
            if ($cell !== '.') {
                $antennas[] = new Antenna($x, $y, $cell);
            }
        }
    }

    // Calculate antinodes for each pair of antennas with the same frequency
    foreach ($antennas as $antenna1) {
        foreach ($antennas as $antenna2) {
            if ($antenna1->frequency === $antenna2->frequency && $antenna1 !== $antenna2) {
                $dx = $antenna2->x - $antenna1->x;
                $dy = $antenna2->y - $antenna1->y;

                $antinode1X = $antenna1->x - $dx;
                $antinode1Y = $antenna1->y - $dy;

                $antinode2X = $antenna2->x + $dx;
                $antinode2Y = $antenna2->y + $dy;

                // Check if the antinodes are within the bounds of the map
                if ($antinode1X >= 0 && $antinode1X < strlen($input[0]) && $antinode1Y >= 0 && $antinode1Y < count($input)) {
                    if (!isset($antinodes["$antinode1X,$antinode1Y"])) {
                        $antinodes["$antinode1X,$antinode1Y"] = true;
                        $map[$antinode1Y] = substr_replace($map[$antinode1Y], '#', $antinode1X, 1);
                    }
                }

                if ($antinode2X >= 0 && $antinode2X < strlen($input[0]) && $antinode2Y >= 0 && $antinode2Y < count($input)) {
                    if (!isset($antinodes["$antinode2X,$antinode2Y"])) {
                        $antinodes["$antinode2X,$antinode2Y"] = true;
                        $map[$antinode2Y] = substr_replace($map[$antinode2Y], '#', $antinode2X, 1);
                    }
                }
            }
        }
    }

    // Output the map with antinodes marked
    foreach ($map as $row) {
        echo $row . "\n";
    }

    // Count the unique locations with antinodes
    $count = 0;
    foreach ($map as $row) {
        $count += substr_count($row, '#');
    }

    $end = microtime(true);
    $memory = memory_get_usage() - $memory;

    echo "Time: " . ($end - $start) . " seconds\n";
    echo "Memory: $memory bytes\n";
    echo "Result: $count\n";

    return $count;
}

$input = file('input08.txt', FILE_IGNORE_NEW_LINES);
calculateImpact($input);