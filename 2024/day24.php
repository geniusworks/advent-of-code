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

const GATE_PAIR_COUNT = 4;

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

function solvePart2($input): string
{
    $wires = [];
    $gates = [];
    $isParsingGates = false;

    // Parse input
    foreach ($input as $line) {
        $line = trim($line);
        if (empty($line)) {
            $isParsingGates = true;
            continue;
        }

        if (!$isParsingGates) {
            if (preg_match('/^(\w+):\s*(\d+)$/', $line, $matches)) {
                $wires[$matches[1]] = intval($matches[2]);
            }
        } else {
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

    // Function to check if a gate is correctly connected
    $isCorrectGate = function ($gate) {
        if (!str_starts_with($gate['output'], 'z')) {
            return true;
        } // Non-z wires are always correct

        $zIndex = substr($gate['output'], 1);
        $expectedX = "x$zIndex";
        $expectedY = "y$zIndex";

        return ($gate['type'] === 'AND' &&
            (($gate['input1'] === $expectedX && $gate['input2'] === $expectedY) ||
                ($gate['input1'] === $expectedY && $gate['input2'] === $expectedX)));
    };

    // Find all gate outputs
    $outputs = array_unique(array_column($gates, 'output'));
    sort($outputs);

    // Try all possible combinations of GATE_PAIR_COUNT pairs of wire swaps
    $swapPairs = [];
    $used = [];

    $findSwapPairs = function($depth = 0) use ($outputs, &$swapPairs, &$used, &$findSwapPairs, $gates, $isCorrectGate) {
        if ($depth === GATE_PAIR_COUNT) {
            // Create swap map from all pairs
            $swapMap = [];
            foreach ($swapPairs as $pair) {
                $swapMap[$pair[0]] = $pair[1];
                $swapMap[$pair[1]] = $pair[0];
            }

            // Check if this configuration makes all gates correct
            foreach ($gates as $gate) {
                $swappedGate = $gate;
                if (isset($swapMap[$gate['output']])) {
                    $swappedGate['output'] = $swapMap[$gate['output']];
                }
                if (!$isCorrectGate($swappedGate)) {
                    return false;
                }
            }

            // Found a valid solution
            $swappedWires = [];
            foreach ($swapPairs as $pair) {
                $swappedWires[] = $pair[0];
                $swappedWires[] = $pair[1];
            }
            sort($swappedWires);
            return implode(',', $swappedWires);
        }

        // Try each possible pair
        $numOutputs = count($outputs);
        for ($i = 0; $i < $numOutputs - 1; $i++) {
            if (isset($used[$i])) continue;
            for ($j = $i + 1; $j < $numOutputs; $j++) {
                if (isset($used[$j])) continue;

                // Try this pair
                $used[$i] = $used[$j] = true;
                $swapPairs[] = [$outputs[$i], $outputs[$j]];

                $result = $findSwapPairs($depth + 1);
                if ($result !== false) {
                    return $result;
                }

                // Backtrack
                array_pop($swapPairs);
                unset($used[$i], $used[$j]);
            }
        }

        return false;
    };

    $result = $findSwapPairs();
    return $result === false ? "No solution found" : $result;
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
echo "Sorted names of the eight wires involved in a swap: {$result2}" . PHP_EOL;
$profiler->reportProfile();