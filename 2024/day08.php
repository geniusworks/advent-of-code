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

function calculateImpact($input, $antennas): int
{
    $antinodes = [];
    $map = $input;

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

    return $count;
}

function calculateImpact2($input, $antennas): int
{
    $antinodes = [];
    $map = $input;

    // Calculate antinodes for each pair of antennas with the same frequency
    foreach ($antennas as $antenna1) {
        foreach ($antennas as $antenna2) {
            if ($antenna1->frequency === $antenna2->frequency && $antenna1 !== $antenna2) {
                $dx = $antenna2->x - $antenna1->x;
                $dy = $antenna2->y - $antenna1->y;

                // Check all points in line with the two antennas
                $gcd = getGreatestCommonDivisor(abs($dx), abs($dy));
                $dx /= $gcd;
                $dy /= $gcd;
                $length = max(abs($dx * strlen($map[0])), abs($dy * count($map)));
                for ($i = -$length; $i <= $length; $i++) {
                    $x = $antenna1->x + $i * $dx;
                    $y = $antenna1->y + $i * $dy;
                    if ($x >= 0 && $x < strlen($map[0]) && $y >= 0 && $y < count($map)) {
                        markAntinode($map, (int)$x, (int)$y, $antinodes);
                    }
                }
            }
        }
    }

    return count($antinodes);
}

function getGreatestCommonDivisor($a, $b)
{
    if ($b === 0) {
        return $a;
    } else {
        return getGreatestCommonDivisor($b, $a % $b);
    }
}

$input = file('input08.txt', FILE_IGNORE_NEW_LINES);

$antennas = [];
foreach ($input as $y => $row) {
    foreach (str_split($row) as $x => $cell) {
        if ($cell !== '.') {
            $antennas[] = new Antenna($x, $y, $cell);
        }
    }
}

$start_time = microtime(true);
$start_memory = memory_get_usage(true);

$resultPart1 = solve($input);
echo "Part 1: Calibration result = {$resultPart1['calibrationResult']}\n";
echo "Execution time: " . ($resultPart1['executionTime']) . " seconds\n";
echo "Memory usage: " . ($resultPart1['memoryUsage'] - $start_memory) . " bytes\n";

$part1_peak_memory = memory_get_peak_usage(true);

$resultPart2 = solvePart2($input);
echo "\nPart 2: Calibration result = {$resultPart2['calibrationResult']}\n";
echo "Execution time: " . ($resultPart2['executionTime']) . " seconds\n";
echo "Memory usage: " . (memory_get_peak_usage(true) - $part1_peak_memory) . " bytes\n";

$end_time = microtime(true);
$end_memory = memory_get_usage(true);
echo "\nTotal execution time: " . ($end_time - $start_time) . " seconds\n";
echo "Total memory usage: " . ($end_memory - $start_memory) . " bytes\n";
