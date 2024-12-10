<?php
/**
 * Advent of Code 2024
 * Day 10: Hoof It
 *
 * @author Martin Diekhoff
 * @link https://adventofcode.com/2024/day/10
 */

const DATA_INPUT_FILE = 'input10.txt';

require_once __DIR__ . '/../' . 'bootstrap.php';

$input = DataImporter::importFromFileWithDefaultFlags(__DIR__ . '/' . DATA_INPUT_FILE);

// Split each row into an array of columns
$input = array_map(function($row) {
    return array_map('intval', str_split($row));
}, $input);

// Define the possible movements (up, down, left, right)
$moves = [[0, 1], [0, -1], [1, 0], [-1, 0]];

// Function to calculate the score for a trailhead
function calculateScore($map, $x, $y, $moves): int
{
    $score = 0;
    $stack = [[$x, $y]];
    $visited = [];
    while ($stack) {
        [$cx, $cy] = array_pop($stack);
        if (in_array([$cx, $cy], $visited)) {
            continue;
        }
        $visited[] = [$cx, $cy];

        // Check if this position is a 9-height position
        if ($map[$cy][$cx] == 9) {
            $score++;
        }

        // Explore neighboring positions
        foreach ($moves as $move) {
            $nx = $cx + $move[0];
            $ny = $cy + $move[1];
            if (isset($map[$ny][$nx]) && $map[$ny][$nx] == $map[$cy][$cx] + 1) {
                $stack[] = [$nx, $ny];
            }
        }
    }
    return $score;
}

function Part1($input): int
{
    $totalScore = 0;
    foreach ($input as $y => $row) {
        foreach ($row as $x => $height) {
            if ($height == 0) {
                $totalScore += calculateScore($input, $x, $y, $moves);
            }
        }
    }
    return $totalScore;
}

function Part2($input): int
{
    $totalRating = 0;
    $trailheads = [];
    $trailEnds = [];

    // Find all trailheads (0) and trail ends (9)
    foreach ($input as $y => $row) {
        foreach ($row as $x => $height) {
            if ($height == 0) {
                $trailheads[] = [$x, $y];
            } elseif ($height == 9) {
                $trailEnds[] = [$x, $y];
            }
        }
    }

    // Discover all unique trails leading from each 0 to each connected/possible 9
    foreach ($trailheads as $trailhead) {
        $rating = 0;
        foreach ($trailEnds as $trailEnd) {
            $paths = [];
            $stack = [[$trailhead]];
            while ($stack) {
                $path = array_pop($stack);
                $cx = $path[count($path) - 1][0];
                $cy = $path[count($path) - 1][1];

                // Check if this position is the trail end
                if ($trailEnd[0] == $cx && $trailEnd[1] == $cy) {
                    $paths[] = $path;
                }

                // Explore neighboring positions
                foreach ($moves as $move) {
                    $nx = $cx + $move[0];
                    $ny = $cy + $move[1];
                    if (isset($input[$ny][$nx]) && $input[$ny][$nx] == $input[$cy][$cx] + 1) {
                        $newPath = $path;
                        $newPath[] = [$nx, $ny];
                        $stack[] = $newPath;
                    }
                }
            }
            $rating += count($paths);
        }
        $totalRating += $rating;
    }
    return $totalRating;
}

// Part 1

$profiler = new Profiler('Part 1');
$profiler->startProfile();
$result1 = Part1($input);
$profiler->stopProfile();
echo "Sum of trailhead scores: {$result1}" . PHP_EOL;
$profiler->reportProfile();

// Part 2

$profiler = new Profiler('Part 2');
$profiler->startProfile();
$result2 = Part2($input);
$profiler->stopProfile();
echo "Sum of trailhead ratings: {$result2}" . PHP_EOL;
$profiler->reportProfile();