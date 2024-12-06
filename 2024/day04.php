<?php

// Advent of Code 2024 Day 4
// Martin Diekhoff

function find_xmas_occurrences($lines): int
{
    $xmasOccurrences = 0;

    // Function to check occurrence with preserved original logic
    $checkOccurrence = function($chars, $pattern) {
        return implode('', $chars) === $pattern;
    };

    foreach ($lines as $i => $line) {
        for ($j = 0; $j < strlen($line); $j++) {
            // Horizontal checks
            if ($j + 3 < strlen($line)) {
                // Forward horizontal
                $horizontalChars = [
                    $line[$j],
                    $line[$j+1],
                    $line[$j+2],
                    $line[$j+3]
                ];
                if ($checkOccurrence($horizontalChars, 'XMAS')) {
                    $xmasOccurrences++;
                }
                if ($checkOccurrence($horizontalChars, 'SAMX')) {
                    $xmasOccurrences++;
                }
            }

            // Vertical checks
            if ($i + 3 < count($lines) && $j < strlen($line)) {
                $verticalChars = [
                    $line[$j],
                    $lines[$i + 1][$j],
                    $lines[$i + 2][$j],
                    $lines[$i + 3][$j]
                ];
                if ($checkOccurrence($verticalChars, 'XMAS')) {
                    $xmasOccurrences++;
                }
                if ($checkOccurrence($verticalChars, 'SAMX')) {
                    $xmasOccurrences++;
                }
            }

            // Diagonal top-left to bottom-right checks
            if ($i + 3 < count($lines) && $j + 3 < strlen($line)) {
                $diagonalChars = [
                    $line[$j],
                    $lines[$i + 1][$j + 1],
                    $lines[$i + 2][$j + 2],
                    $lines[$i + 3][$j + 3]
                ];
                if ($checkOccurrence($diagonalChars, 'XMAS')) {
                    $xmasOccurrences++;
                }
                if ($checkOccurrence($diagonalChars, 'SAMX')) {
                    $xmasOccurrences++;
                }
            }

            // Diagonal bottom-left to top-right checks
            if ($i - 3 >= 0 && $j + 3 < strlen($line)) {
                $diagonalChars = [
                    $line[$j],
                    $lines[$i - 1][$j + 1],
                    $lines[$i - 2][$j + 2],
                    $lines[$i - 3][$j + 3]
                ];
                if ($checkOccurrence($diagonalChars, 'XMAS')) {
                    $xmasOccurrences++;
                }
                if ($checkOccurrence($diagonalChars, 'SAMX')) {
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

// Main script
$start_time = microtime(true);
$start_memory = memory_get_usage(true);

$lines = file('input04.txt', FILE_IGNORE_NEW_LINES);

// Part 1
$xmasOccurrences = find_xmas_occurrences($lines);

// Part 2
$a_positions = find_a_positions($lines);
$valid_positions = validate_a_positions($lines, $a_positions);

echo "Total occurrences of XMAS: $xmasOccurrences\n";
echo "Total occurrences of X-MAS: " . count($valid_positions) . "\n";

$end_time = microtime(true);
$end_memory = memory_get_usage(true);

echo "Time elapsed: " . ($end_time - $start_time) . " seconds\n";
echo "Memory usage: " . ($end_memory - $start_memory) . " bytes\n";
