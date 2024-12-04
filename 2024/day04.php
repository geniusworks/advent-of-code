<?php

$lines = file('input04.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

// Part 1

// Initialize count of XMAS occurrences
$xmasCount = 0;

// Iterate over each line in the word search puzzle
for ($i = 0; $i < count($lines); $i++) {
    // Iterate over each character in the line
    for ($j = 0; $j < strlen($lines[$i]); $j++) {
        // Check for horizontal occurrence of XMAS
        if ($j < strlen($lines[$i]) - 3 && substr($lines[$i], $j, 4) === 'XMAS') {
            $xmasCount++;
        }

        // Check for vertical occurrence of XMAS
        if ($i < count($lines) - 3 && $j < strlen($lines[$i])) {
            $verticalString = $lines[$i][$j] . $lines[$i + 1][$j] . $lines[$i + 2][$j] . $lines[$i + 3][$j];
            if ($verticalString === 'XMAS') {
                $xmasCount++;
            }
        }

        // Check for diagonal occurrence of XMAS (top-left to bottom-right)
        if ($i < count($lines) - 3 && $j < strlen($lines[$i]) - 3) {
            $diagonalString = $lines[$i][$j] . $lines[$i + 1][$j + 1] . $lines[$i + 2][$j + 2] . $lines[$i + 3][$j + 3];
            if ($diagonalString === 'XMAS') {
                $xmasCount++;
            }
        }

        // Check for diagonal occurrence of XMAS (bottom-left to top-right)
        if ($i > 2 && $j < strlen($lines[$i]) - 3) {
            $diagonalString = $lines[$i][$j] . $lines[$i - 1][$j + 1] . $lines[$i - 2][$j + 2] . $lines[$i - 3][$j + 3];
            if ($diagonalString === 'XMAS') {
                $xmasCount++;
            }
        }

        // Check for reverse horizontal occurrence of XMAS
        if ($j < strlen($lines[$i]) - 3 && substr($lines[$i], $j, 4) === 'SAMX') {
            $xmasCount++;
        }

        // Check for reverse vertical occurrence of XMAS
        if ($i < count($lines) - 3 && $j < strlen($lines[$i])) {
            $verticalString = $lines[$i][$j] . $lines[$i + 1][$j] . $lines[$i + 2][$j] . $lines[$i + 3][$j];
            if ($verticalString === 'SAMX') {
                $xmasCount++;
            }
        }

        // Check for reverse diagonal occurrence of XMAS (top-left to bottom-right)
        if ($i < count($lines) - 3 && $j < strlen($lines[$i]) - 3) {
            $diagonalString = $lines[$i][$j] . $lines[$i + 1][$j + 1] . $lines[$i + 2][$j + 2] . $lines[$i + 3][$j + 3];
            if ($diagonalString === 'SAMX') {
                $xmasCount++;
            }
        }

        // Check for reverse diagonal occurrence of XMAS (bottom-left to top-right)
        if ($i > 2 && $j < strlen($lines[$i]) - 3) {
            $diagonalString = $lines[$i][$j] . $lines[$i - 1][$j + 1] . $lines[$i - 2][$j + 2] . $lines[$i - 3][$j + 3];
            if ($diagonalString === 'SAMX') {
                $xmasCount++;
            }
        }
    }
}

echo "Total occurrences of XMAS: $xmasCount\n";

// Part 2

