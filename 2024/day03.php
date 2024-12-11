<?php
/**
 * Advent of Code 2024
 * Day 3: Mull It Over
 *
 * @author Martin Diekhoff
 * @link https://adventofcode.com/2024/day/3
 */

require_once '../bootstrap.php';

$input = DataImporter::importFromFileWithDefaultFlags('input03.txt');

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

// Part 1

$profiler = new Profiler();
$profiler->startProfile();
$sumOfMultiplications = calculateResult($input, 1);
$profiler->stopProfile();
echo "Sum of multiplications: {$sumOfMultiplications}" . PHP_EOL;
$profiler->reportProfile();

// Part 1

$profiler = new Profiler();
$profiler->startProfile();
$sumOfEnabledMultiplications = calculateResult($input, 2);
$profiler->stopProfile();
echo "Sum of enabled multiplications: {$sumOfEnabledMultiplications}" . PHP_EOL;
$profiler->reportProfile();