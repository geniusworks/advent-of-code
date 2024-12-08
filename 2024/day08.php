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

function markAntinode(&$map, $x, $y, &$antinodes): void
{
    if ($x >= 0 && $x < strlen($map[0]) && $y >= 0 && $y < count($map)) {
        if (!isset($antinodes["$x,$y"])) {
            $antinodes["$x,$y"] = true;
            $map[$y] = substr_replace($map[$y], '#', $x, 1);
        }
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

                markAntinode($map, $antinode1X, $antinode1Y, $antinodes);
                markAntinode($map, $antinode2X, $antinode2Y, $antinodes);
            }
        }
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
    echo "Number of unique antinodes: $count\n";

    return $count;
}

$input = file('input08.txt', FILE_IGNORE_NEW_LINES);
calculateImpact($input);
