<?php
/**
 * Advent of Code 2024
 * Day 1: Historian Hysteria
 *
 * @author Martin Diekhoff
 * @link https://adventofcode.com/2024/day/1
 */

$start_time = microtime(true);

$input = file('input01.txt', FILE_IGNORE_NEW_LINES);

$leftList = [];
$rightList = [];

foreach ($input as $line) {
    [$left, $right] = array_map('intval', explode('   ', $line));
    $leftList[] = $left;
    $rightList[] = $right;
}

sort($leftList);
sort($rightList);

$totalDistance = array_sum(array_map(function ($left, $right) {
    return abs($left - $right);
}, $leftList, $rightList));

echo "Total Distance: $totalDistance" . PHP_EOL;

$similarityScore = 0;
foreach ($leftList as $left) {
    $similarityScore += $left * count(array_keys($rightList, $left));
}

echo "Similarity Score: $similarityScore" . PHP_EOL;

$end_time = microtime(true);

echo "Time elapsed: " . ($end_time - $start_time) . " seconds" . PHP_EOL;
echo "Memory usage: " . memory_get_peak_usage(true) . " bytes\n";
