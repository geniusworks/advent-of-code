<?php
/**
 * Advent of Code 2024
 * Day 1: Historian Hysteria
 *
 * @author Martin Diekhoff
 * @link https://adventofcode.com/2024/day/1
 */

require_once '../bootstrap.php';

$input = DataImporter::importFromFileWithDefaultFlags('input01.txt');

// Part 1

$profiler = new Profiler('Part 1');
$profiler->startProfile();

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

$profiler->stopProfile();
echo "Total Distance: {$totalDistance}" . PHP_EOL;
$profiler->reportProfile();

// Part 2

$profiler = new Profiler('Part 2');
$profiler->startProfile();

$similarityScore = 0;
foreach ($leftList as $left) {
    $similarityScore += $left * count(array_keys($rightList, $left));
}

$profiler->stopProfile();
echo "Similarity Score: {$similarityScore}" . PHP_EOL;
$profiler->reportProfile();