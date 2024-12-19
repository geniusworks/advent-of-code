<?php
/**
 * Advent of Code 2024
 * Day 19: Linen Layout
 *
 * @author Martin Diekhoff
 * @link https://adventofcode.com/2024/day/19
 */

const DATA_INPUT_FILE = 'input19.txt';

require_once __DIR__ . '/../' . 'bootstrap.php';

$input = DataImporter::importFromFileWithDefaultFlags(__DIR__ . '/' . DATA_INPUT_FILE);

function canMakeDesign($availablePatterns, $design): bool
{
    // Trim spaces and normalize the design
    $design = trim($design);
    if (empty($design)) {
        return false;
    }

    // Initialize DP array - dp[i] represents if we can make substring of design up to position i
    $dp = [true]; // Empty string is always possible

    // For each position in the design
    for ($i = 1; $i <= strlen($design); $i++) {
        $dp[$i] = false;

        // Try each pattern at current position
        foreach ($availablePatterns as $pattern) {
            $pattern = trim($pattern);
            $patternLength = strlen($pattern);

            // Check if pattern can be used at current position
            if ($i >= $patternLength) {
                $designSubstring = substr($design, $i - $patternLength, $patternLength);
                if ($designSubstring === $pattern && $dp[$i - $patternLength]) {
                    $dp[$i] = true;
                    break;
                }
            }
        }
    }

    return $dp[strlen($design)];
}

function countPossibleDesigns($input): int
{
    // Parse input
    $lines = array_filter($input, 'strlen');
    $patterns = array_map('trim', explode(',', $lines[0]));
    $designs = array_slice($lines, 1);

    $possibleCount = 0;
    foreach ($designs as $design) {
        if (canMakeDesign($patterns, $design)) {
            $possibleCount++;
        }
    }

    return $possibleCount;
}

function getCombinationsCount($availablePatterns, $design)
{
    $design = trim($design);
    if (empty($design)) {
        return 0;
    }

    // dp[i] will store the number of ways to make the design up to position i
    $dp = array_fill(0, strlen($design) + 1, 0);
    $dp[0] = 1; // Empty string can be made in one way

    // For each position in the design
    for ($i = 1; $i <= strlen($design); $i++) {
        // Try each pattern at the current position
        foreach ($availablePatterns as $pattern) {
            $pattern = trim($pattern);
            $patternLength = strlen($pattern);

            // Check if pattern can be used at current position
            if ($i >= $patternLength) {
                $designSubstring = substr($design, $i - $patternLength, $patternLength);
                if ($designSubstring === $pattern) {
                    // Add the number of ways to make the design up to the position
                    // before this pattern
                    $dp[$i] += $dp[$i - $patternLength];
                }
            }
        }
    }

    return $dp[strlen($design)];
}

function countPossibleDesignCombinations($input)
{
    $lines = array_filter($input, 'strlen');
    $patterns = array_map('trim', explode(',', $lines[0]));
    $designs = array_slice($lines, 1);

    $totalCombinations = 0;
    foreach ($designs as $design) {
        $combinations = getCombinationsCount($patterns, $design);
        // echo "$design: $combinations combinations\n";
        $totalCombinations += $combinations;
    }

    return $totalCombinations;
}

// Part 1

$profiler = new Profiler();
$profiler->startProfile();
$result1 = countPossibleDesigns($input);
$profiler->stopProfile();
echo "Number of possible designs: {$result1}" . PHP_EOL;
$profiler->reportProfile();

// Part 2

$profiler = new Profiler();
$profiler->startProfile();
$result2 = countPossibleDesignCombinations($input);
$profiler->stopProfile();
echo "Number of possible design combinations: {$result2}" . PHP_EOL;
$profiler->reportProfile();