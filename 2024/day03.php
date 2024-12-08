<?php
/**
 * Advent of Code 2024
 * Day 3: Mull It Over
 *
 * @author Martin Diekhoff
 * @link https://adventofcode.com/2024/day/3
 */

$start_time = microtime(true);

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

echo "Time elapsed: " . ($end_time - $start_time) . " seconds" . PHP_EOL;
echo "Memory usage: " . memory_get_peak_usage(true) . " bytes\n";
