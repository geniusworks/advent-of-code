<?php
/**
 * Advent of Code 2024
 * Day 24: Crossed Wires
 *
 * @author Martin Diekhoff
 * @link https://adventofcode.com/2024/day/24
 */

const DATA_INPUT_FILE = 'input24.txt';

require_once __DIR__ . '/../bootstrap.php';

$input = DataImporter::importFromFileWithDefaultFlags(__DIR__ . '/' . DATA_INPUT_FILE);

const REQUIRED_GATE_PAIRS = 4;  // Number of wire pairs that need to be swapped

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

function findGate($input1, $gateType, $input2)
{
    global $gatesByInputs;
    if (isset($gatesByInputs[$key = "$input1,$gateType,$input2"])) {
        return $gatesByInputs[$key];
    }
    if (isset($gatesByInputs[$key = "$input2,$gateType,$input1"])) {
        return $gatesByInputs[$key];
    }
    return false;
}

function solvePart2($input): string
{
    global $gatesByInputs;
    $gatesByInputs = [];
    $pairsFound = 0;

    $isParsingGates = false;
    foreach ($input as $line) {
        $line = trim($line);
        if (empty($line)) {
            $isParsingGates = true;
            continue;
        }

        if ($isParsingGates) {
            $parts = explode(" ", $line);
            $gatesByInputs["$parts[0],$parts[1],$parts[2]"] = $parts[4];
            $gatesByInputs["$parts[2],$parts[1],$parts[0]"] = $parts[4];
        }
    }

    for ($swappedWires = [], $carryIn = $bitPos = 0; $bitPos < 45 && $pairsFound < REQUIRED_GATE_PAIRS; $bitPos++, $carryIn = $carryOut) {
        $posStr = substr("0{$bitPos}", -2);

        if ($bitPos == 0) {
            $carryOut = findGate("x$posStr", "AND", "y$posStr");
            assert($carryOut);
            continue;
        }

        $xorResult = findGate("x$posStr", "XOR", "y$posStr");
        $andResult = findGate("x$posStr", "AND", "y$posStr");
        $carryAndXor = findGate($carryIn, "AND", $xorResult);

        if (!$carryAndXor) {
            [$andResult, $xorResult] = [$xorResult, $andResult];
            array_push($swappedWires, $xorResult, $andResult);
            $pairsFound++;
            $carryAndXor = findGate($carryIn, "AND", $xorResult);
        }

        $sum = findGate($carryIn, "XOR", $xorResult);

        if ($xorResult[0] == 'z' && $pairsFound < REQUIRED_GATE_PAIRS) {
            [$xorResult, $sum] = [$sum, $xorResult];
            array_push($swappedWires, $xorResult, $sum);
            $pairsFound++;
        }

        if ($andResult[0] == 'z' && $pairsFound < REQUIRED_GATE_PAIRS) {
            [$andResult, $sum] = [$sum, $andResult];
            array_push($swappedWires, $andResult, $sum);
            $pairsFound++;
        }

        if ($carryAndXor[0] == 'z' && $pairsFound < REQUIRED_GATE_PAIRS) {
            [$carryAndXor, $sum] = [$sum, $carryAndXor];
            array_push($swappedWires, $carryAndXor, $sum);
            $pairsFound++;
        }

        $carryOut = findGate($carryAndXor, "OR", $andResult);

        if ($carryOut && $sum && $carryOut[0] == 'z' && $carryOut !== "z45" && $pairsFound < REQUIRED_GATE_PAIRS) {
            [$carryOut, $sum] = [$sum, $carryOut];
            array_push($swappedWires, $carryOut, $sum);
            $pairsFound++;
        }
    }

    assert(
        $pairsFound === REQUIRED_GATE_PAIRS,
        "Expected exactly " . REQUIRED_GATE_PAIRS . " gate pairs, found $pairsFound",
    );
    sort($swappedWires);

    return implode(',', $swappedWires);
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