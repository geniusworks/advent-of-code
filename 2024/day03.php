<?php

// Advent of Code 2024 Day 3
// Martin Diekhoff

$start_time = microtime(true);
$start_memory = memory_get_usage(true);

$input = file('input03.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

function calculateResult($input, $part): float|int
{
    $result = 0;
    $isEnabled = true;

    foreach ($input as $line) {
        preg_match_all('/(do\(\))|(don\'t\(\))|mul\((\d+),(\d+)\)/', $line, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            if ($match[1] == 'do()') {
                $isEnabled = true;
            } elseif ($match[2] == "don't()") {
                $isEnabled = false;
            } elseif ($match[3] && ($part == 1 || $isEnabled)) {
                $result += (int)$match[3] * (int)$match[4];
            }
        }
    }

    return $result;
}

echo "Final result (Part 1): " . calculateResult($input, 1) . PHP_EOL;
echo "Final result (Part 2): " . calculateResult($input, 2) . PHP_EOL;

$end_time = microtime(true);
$end_memory = memory_get_usage(true);

echo "Time elapsed: " . ($end_time - $start_time) . " seconds" . PHP_EOL;
echo "Memory usage: " . ($end_memory - $start_memory) . " bytes" . PHP_EOL;