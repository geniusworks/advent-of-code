<?php
/**
 * Advent of Code 2024
 * Day 4: Ceres Search
 *
 * @author Martin Diekhoff
 * @link https://adventofcode.com/2024/day/4
 */

require_once '../bootstrap.php';

$input = DataImporter::importFromFileWithDefaultFlags('input04.txt');

function find_xmas_occurrences($input): int
{
    $xmasOccurrences = 0;

    foreach ($input as $i => $line) {
        for ($j = 0; $j < strlen($line); $j++) {
            // Horizontal checks
            if ($j + 3 < strlen($line)) {
                // Forward horizontal
                if (substr($line, $j, 4) === 'XMAS') {
                    $xmasOccurrences++;
                }
                if (substr($line, $j, 4) === 'SAMX') {
                    $xmasOccurrences++;
                }
            }

            // Vertical checks
            if ($i + 3 < count($input) && $j < strlen($line)) {
                $verticalChars = $line[$j] . $input[$i + 1][$j] . $input[$i + 2][$j] . $input[$i + 3][$j];
                if ($verticalChars === 'XMAS') {
                    $xmasOccurrences++;
                }
                if ($verticalChars === 'SAMX') {
                    $xmasOccurrences++;
                }
            }

            // Diagonal top-left to bottom-right checks
            if ($i + 3 < count($input) && $j + 3 < strlen($line)) {
                $diagonalChars = $line[$j] . $input[$i + 1][$j + 1] . $input[$i + 2][$j + 2] . $input[$i + 3][$j + 3];
                if ($diagonalChars === 'XMAS') {
                    $xmasOccurrences++;
                }
                if ($diagonalChars === 'SAMX') {
                    $xmasOccurrences++;
                }
            }

            // Diagonal bottom-left to top-right checks
            if ($i - 3 >= 0 && $j + 3 < strlen($line)) {
                $diagonalChars = $line[$j] . $input[$i - 1][$j + 1] . $input[$i - 2][$j + 2] . $input[$i - 3][$j + 3];
                if ($diagonalChars === 'XMAS') {
                    $xmasOccurrences++;
                }
                if ($diagonalChars === 'SAMX') {
                    $xmasOccurrences++;
                }
            }
        }
    }

    return $xmasOccurrences;
}

function find_a_positions($grid): array
{
    $a_positions = [];
    $rows = count($grid);
    $cols = strlen($grid[0]);

    for ($i = 1; $i < $rows - 1; $i++) {
        for ($j = 1; $j < $cols - 1; $j++) {
            if ($grid[$i][$j] == 'A') {
                $a_positions[] = [$i, $j];
            }
        }
    }

    return $a_positions;
}

function validate_a_positions($grid, $a_positions): array
{
    $valid_positions = [];

    foreach ($a_positions as $position) {
        [$i, $j] = $position;

        $match_count = 0;

        // Check top-left to bottom-right diagonal
        if ($grid[$i - 1][$j - 1] == 'M' && $grid[$i + 1][$j + 1] == 'S') {
            $match_count++;
        }

        // Check top-right to bottom-left diagonal
        if ($grid[$i - 1][$j + 1] == 'M' && $grid[$i + 1][$j - 1] == 'S') {
            $match_count++;
        }

        // Check top-left to bottom-right diagonal (reverse)
        if ($grid[$i - 1][$j - 1] == 'S' && $grid[$i + 1][$j + 1] == 'M') {
            $match_count++;
        }

        // Check top-right to bottom-left diagonal (reverse)
        if ($grid[$i - 1][$j + 1] == 'S' && $grid[$i + 1][$j - 1] == 'M') {
            $match_count++;
        }

        if ($match_count == 2) {
            $valid_positions[] = $position;
        }
    }

    return $valid_positions;
}

// Part 1

$profiler = new Profiler('Part 1');
$profiler->startProfile();
$xmasOccurrences = find_xmas_occurrences($input);
$profiler->stopProfile();
echo "Total occurrences of XMAS: {$xmasOccurrences}" . PHP_EOL;
$profiler->reportProfile();

// Part 2

$profiler = new Profiler('Part 2');
$profiler->startProfile();
$a_positions = find_a_positions($input);
$xmasCrisscrossedOccurrences = count(validate_a_positions($input, $a_positions));
$profiler->stopProfile();
echo "Total occurrences of X-MAS: {$xmasCrisscrossedOccurrences}" . PHP_EOL;
$profiler->reportProfile();