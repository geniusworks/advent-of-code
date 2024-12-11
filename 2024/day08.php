<?php
/**
 * Advent of Code 2024
 * Day 8: Resonant Collinearity
 *
 * @author Martin Diekhoff
 * @link https://adventofcode.com/2024/day/8
 */

const DATA_INPUT_FILE = 'input08.txt';

require_once __DIR__ . '/../' . 'bootstrap.php';

$input = DataImporter::importFromFileWithDefaultFlags(__DIR__ . '/' . DATA_INPUT_FILE);

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

// Functions

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

$antennas = [];
foreach ($input as $y => $row) {
    foreach (str_split($row) as $x => $cell) {
        if ($cell !== '.') {
            $antennas[] = new Antenna($x, $y, $cell);
        }
    }
}

// Part 1

$profiler = new Profiler();
$profiler->startProfile();
$resultPart1 = calculateImpact($input, $antennas);
$profiler->stopProfile();
echo "Calibration result = {$resultPart1}" . PHP_EOL;
$profiler->reportProfile();

// Part 2

$profiler = new Profiler();
$profiler->startProfile();
$resultPart2 = calculateImpact2($input, $antennas);
$profiler->stopProfile();
echo "Calibration result = {$resultPart2}" . PHP_EOL;
$profiler->reportProfile();