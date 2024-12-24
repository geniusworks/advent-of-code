<?php
/**
 * Advent of Code 2024
 * Day 24: Crossed Wires
 *
 * @author Martin Diekhoff
 * @link https://adventofcode.com/2024/day/24
 */

const DATA_INPUT_FILE = 'input24.txt';

require_once __DIR__ . '/../' . 'bootstrap.php';

$input = DataImporter::importFromFileWithDefaultFlags(__DIR__ . '/' . DATA_INPUT_FILE);

function solvePart1($input): float|int
{
    $lines = $input;
    $wires = [];
    $gates = [];
    $isParsingGates = false;

    // Parse input
    foreach ($lines as $line) {
        $line = trim($line);
        if (empty($line)) {
            $isParsingGates = true;
            continue;
        }

        if (!$isParsingGates) {
            // Parse initial wire values
            if (preg_match('/^(\w+):\s*(\d+)$/', $line, $matches)) {
                $wires[$matches[1]] = intval($matches[2]);
            }
        } else {
            // Parse gate definitions
            if (preg_match('/^(\w+)\s+(AND|OR|XOR)\s+(\w+)\s+->\s+(\w+)$/', $line, $matches)) {
                $gates[] = [
                    'type' => $matches[2],
                    'input1' => $matches[1],
                    'input2' => $matches[3],
                    'output' => $matches[4],
                ];
            }
        }
    }

    // Simulate gates until all z-wires have values
    $changed = true;
    while ($changed) {
        $changed = false;
        foreach ($gates as $gate) {
            if (isset($wires[$gate['output']])) {
                continue; // Skip if output wire already has a value
            }

            if (!isset($wires[$gate['input1']]) || !isset($wires[$gate['input2']])) {
                continue; // Skip if input wires don't have values yet
            }

            $input1 = $wires[$gate['input1']];
            $input2 = $wires[$gate['input2']];

            switch ($gate['type']) {
                case 'AND':
                    $wires[$gate['output']] = $input1 & $input2;
                    break;
                case 'OR':
                    $wires[$gate['output']] = $input1 | $input2;
                    break;
                case 'XOR':
                    $wires[$gate['output']] = $input1 ^ $input2;
                    break;
            }
            $changed = true;
        }
    }

    // Collect all z-wires in order and build binary number
    $zWires = [];
    foreach ($wires as $wire => $value) {
        if (str_starts_with($wire, 'z')) {
            $zWires[$wire] = $value;
        }
    }
    ksort($zWires); // Sort by wire name to ensure correct order

    // Convert binary to decimal
    $binary = '';
    foreach ($zWires as $value) {
        $binary = $value . $binary; // Prepend each bit (z00 is least significant)
    }

    return bindec($binary);
}

function solvePart2($input) {
    // @todo: Solve part 2
}

// Part 1

$profiler = new Profiler();
$profiler->startProfile();
$result1 = solvePart1($input);
$profiler->stopProfile();
echo "Decimal number output on the wires starting with z: {$result1}" . PHP_EOL;
$profiler->reportProfile();

// Part 2

$profiler = new Profiler();
$profiler->startProfile();
$result2 = solvePart2($input);
$profiler->stopProfile();
echo "Result: {$result2}" . PHP_EOL;
$profiler->reportProfile();