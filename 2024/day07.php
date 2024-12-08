<?php
/**
 * Advent of Code 2024
 * Day 7: Bridge Repair
 *
 * @author Martin Diekhoff
 * @link https://adventofcode.com/2024/day/7
 */

require_once '../bootstrap.php';

$input = DataImporter::importFromFileContents('input07.txt');

function parseEquations($input): array
{
    $equations = [];
    foreach (explode("\n", $input) as $line) {
        [$testValue, $numbers] = explode(':', $line);
        $numbers = array_map('intval', explode(' ', trim($numbers)));
        $equations[] = [$testValue, $numbers];
    }
    return $equations;
}

function evaluateEquation($numbers, $operators): int
{
    $result = $numbers[0];
    for ($i = 1; $i < count($numbers); $i++) {
        if ($operators[$i - 1] === '+') {
            $result += $numbers[$i];
        } else {
            $result *= $numbers[$i];
        }
    }
    return $result;
}

function isValidEquation($testValue, $numbers): bool
{
    $operatorCombinations = [
        '+' => ['+', '*'],
        '*' => ['+', '*'],
    ];
    for ($i = 1; $i < count($numbers); $i++) {
        $operatorCombinations[$i] = ['+', '*'];
    }
    foreach (cartesianProduct($operatorCombinations) as $operators) {
        if (evaluateEquation($numbers, $operators) == $testValue) {
            return true;
        }
    }
    return false;
}

function cartesianProduct($arrays): array
{
    $result = [[]];
    foreach ($arrays as $array) {
        $tmp = [];
        foreach ($result as $p) {
            foreach ($array as $item) {
                $tmp[] = array_merge($p, [$item]);
            }
        }
        $result = $tmp;
    }
    return $result;
}

function solve($input): array
{
    $startTime = microtime(true);
    $equations = parseEquations($input);
    $calibrationResult = 0;
    foreach ($equations as [$testValue, $numbers]) {
        if (isValidEquation($testValue, $numbers)) {
            $calibrationResult += $testValue;
        }
    }
    $endTime = microtime(true);
    $memoryUsage = memory_get_usage(true);
    return [
        'calibrationResult' => $calibrationResult,
        'executionTime' => $endTime - $startTime,
        'memoryUsage' => $memoryUsage,
    ];
}

function evaluateEquationPart2($numbers, $operators): int
{
    $result = $numbers[0];
    for ($i = 1; $i < count($numbers); $i++) {
        if ($operators[$i - 1] === '+') {
            $result += $numbers[$i];
        } elseif ($operators[$i - 1] === '*') {
            $result *= $numbers[$i];
        } elseif ($operators[$i - 1] === '||') {
            $result = (int)($result . $numbers[$i]);
        }
    }
    return $result;
}

function isValidEquationPart2($testValue, $numbers): bool
{
    $operators = ['+', '*', '||'];
    $isValid = false;
    generateCombinations($numbers, $operators, 0, [], $testValue, $isValid);
    return $isValid;
}

function generateCombinations($numbers, $operators, $index, $currentOperators, $testValue, &$isValid): void
{
    if ($index === count($numbers) - 1) {
        $result = evaluateEquationPart2($numbers, $currentOperators);
        if ($result == $testValue) {
            $isValid = true;
        }
    } else {
        foreach ($operators as $operator) {
            $newOperators = array_merge($currentOperators, [$operator]);
            generateCombinations($numbers, $operators, $index + 1, $newOperators, $testValue, $isValid);
            if ($isValid) {
                break;
            }
        }
    }
}

function solvePart2($input): array
{
    $startTime = microtime(true);
    $equations = parseEquations($input);
    $calibrationResult = 0;
    foreach ($equations as [$testValue, $numbers]) {
        if (isValidEquationPart2($testValue, $numbers)) {
            $calibrationResult += $testValue;
        }
    }
    $endTime = microtime(true);
    $memoryUsage = memory_get_usage(true);
    return [
        'calibrationResult' => $calibrationResult,
        'executionTime' => $endTime - $startTime,
        'memoryUsage' => $memoryUsage,
    ];
}

// Part 1

$profiler = new Profiler('Part 1');
$profiler->startProfile();
$resultPart1 = solve($input);
$profiler->stopProfile();
echo "Calibration result: {$resultPart1['calibrationResult']}" . PHP_EOL;
$profiler->reportProfile();

// Part 2

$profiler = new Profiler('Part 2');
$profiler->startProfile();
$resultPart2 = solvePart2($input);
$profiler->stopProfile();
echo "Calibration result: {$resultPart2['calibrationResult']}" . PHP_EOL;
$profiler->reportProfile();