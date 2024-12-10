<?php
/**
 * Advent of Code 2024
 * Day 10: 
 *
 * @author Martin Diekhoff
 * @link https://adventofcode.com/2024/day/10
 */

const DATA_INPUT_FILE = 'input10.txt';

require_once __DIR__ . '/../' . 'bootstrap.php';

$input = DataImporter::importFromFileWithDefaultFlags(__DIR__ . '/' . DATA_INPUT_FILE);

// Solution code follows here...

// Part 1

$profiler = new Profiler('Part 1');
$profiler->startProfile();
$result1 = null; // TODO: Calculate the result for part 1.
$profiler->stopProfile();
echo "Result = {$result1}" . PHP_EOL;
$profiler->reportProfile();

// Part 2

$profiler = new Profiler('Part 2');
$profiler->startProfile();
$result2 = null; // TODO: Calculate the result for part 2.
$profiler->stopProfile();
echo "Result = {$result2}" . PHP_EOL;
$profiler->reportProfile();