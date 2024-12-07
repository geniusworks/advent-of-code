<?php

// Advent of Code 2024 Day 7
// Martin Diekhoff

function parseEquations($input)
{
    $equations = [];
    foreach (explode("\n", $input) as $line) {
        [$testValue, $numbers] = explode(':', $line);
        $numbers = array_map('intval', explode(' ', trim($numbers)));
        $equations[] = [$testValue, $numbers];
    }
    return $equations;
}

function evaluateEquation($numbers, $operators)
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

$input = file_get_contents('input07.txt');
$result = solve($input);

echo "Part 1: Calibration result = {$result['calibrationResult']}\n";
echo "Execution time: {$result['executionTime']} seconds\n";
echo "Memory usage: {$result['memoryUsage']} bytes\n";
