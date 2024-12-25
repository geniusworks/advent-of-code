<?php
/**
 * Advent of Code 2024
 * Day 25: Code Chronicle
 *
 * @author Martin Diekhoff
 * @link https://adventofcode.com/2024/day/25
 */

const DATA_INPUT_FILE = 'input25.txt';

require_once __DIR__ . '/../' . 'bootstrap.php';

$input = DataImporter::importFromFileWithDefaultFlags(__DIR__ . '/' . DATA_INPUT_FILE);

function solvePart1(array $input): int
{
    $locks = [];
    $keys = [];
    $currentBlock = [];

    // Parse input into blocks and process each block
    foreach ($input as $line) {
        if (empty(trim($line)) && !empty($currentBlock)) {
            processBlock($currentBlock, $locks, $keys);
            $currentBlock = [];
        } else {
            $currentBlock[] = trim($line);
        }
    }
    if (!empty($currentBlock)) {
        processBlock($currentBlock, $locks, $keys);
    }

    // Count valid pairs where no column's sum exceeds 5
    $validPairs = 0;
    foreach ($locks as $lock) {
        foreach ($keys as $key) {
            if (fits($lock, $key)) {
                $validPairs++;
            }
        }
    }
    return $validPairs;
}

function processBlock(array $block, array &$locks, array &$keys): void
{
    if ($block[0] === '#####') {
        $locks[] = parseSchematic($block);
    } elseif (end($block) === '#####') {
        $keys[] = parseSchematic($block);
    }
}

function parseSchematic(array $schematicLines): array
{
    $heights = [];
    $columnCount = strlen($schematicLines[0]);
    
    for ($col = 0; $col < $columnCount; $col++) {
        $count = 0;
        for ($row = 0; $row < count($schematicLines); $row++) {
            if ($schematicLines[$row][$col] === '#') {
                $count++;
            }
        }
        $heights[] = max(0, $count - 1);
    }
    return $heights;
}

function fits(array $lock, array $key): bool
{
    for ($i = 0; $i < count($lock); $i++) {
        if ($lock[$i] + $key[$i] > 5) {
            return false;
        }
    }
    return true;
}

function solvePart2($input): string
{
    return "Santa returns the chronicle to you.  Mission accomplished.";
}

// Part 1

$profiler = new Profiler();
$profiler->startProfile();
$result1 = solvePart1($input);
$profiler->stopProfile();
echo "Number of unique lock/key pairs fit together without overlapping: {$result1}" . PHP_EOL;
$profiler->reportProfile();

// Part 2

$profiler = new Profiler();
$profiler->startProfile();
$result2 = solvePart2($input);
$profiler->stopProfile();
echo "{$result2}" . PHP_EOL;
$profiler->reportProfile();