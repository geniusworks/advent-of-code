<?php

$lines = file('input.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

// The unusual data (your puzzle input) consists of many reports, one report per line. Each report is a list of numbers called levels that are separated by spaces.
// A report only counts as safe if both of the following are true:
//   1. The levels are either all increasing or all decreasing.
//   2. Any two adjacent levels differ by at least one and at most three.

$safeReports = 0;

foreach ($lines as $line) {
    $levels = array_map('intval', explode(' ', $line));

    $isIncreasing = true;
    $isDecreasing = true;
    $isValid = true;

    for ($i = 1; $i < count($levels); $i++) {
        $diff = abs($levels[$i] - $levels[$i - 1]);

        if ($diff < 1 || $diff > 3) {
            $isValid = false;
            break;
        }

        if ($levels[$i] > $levels[$i - 1]) {
            $isDecreasing = false;
        } elseif ($levels[$i] < $levels[$i - 1]) {
            $isIncreasing = false;
        }
    }

    if ($isValid && ($isIncreasing || $isDecreasing)) {
        $safeReports++;
    }
}

echo "Number of safe reports: $safeReports" . PHP_EOL;

