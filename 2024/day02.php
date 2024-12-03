<?php

$lines = file('input02.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

// Part 1

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

// Part 2

$part2SafeReports = 0;
for ($i = 0; $i < count($lines); $i++) {
    $levels = array_map('intval', explode(' ', $lines[$i]));

    $isSafe = false;
    for ($j = 0; $j < count($levels); $j++) {
        $newLevels = array_merge(array_slice($levels, 0, $j), array_slice($levels, $j + 1));
        $isIncreasing = true;
        $isDecreasing = true;
        $isValid = true;

        for ($k = 1; $k < count($newLevels); $k++) {
            $diff = abs($newLevels[$k] - $newLevels[$k - 1]);

            if ($diff < 1 || $diff > 3) {
                $isValid = false;
                break;
            }

            if ($newLevels[$k] > $newLevels[$k - 1]) {
                $isDecreasing = false;
            } elseif ($newLevels[$k] < $newLevels[$k - 1]) {
                $isIncreasing = false;
            }
        }

        if ($isValid && ($isIncreasing || $isDecreasing)) {
            $isSafe = true;
            break;
        }
    }

    if ($isSafe) {
        $part2SafeReports++;
    }
}

echo "Number of safe reports (Part 1): $safeReports" . PHP_EOL;
echo "Number of safe reports (Part 2): $part2SafeReports" . PHP_EOL;
