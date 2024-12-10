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

function solvePart1($input): int
{
    $equations = parseEquations($input);
    $calibrationResult = 0;
    foreach ($equations as [$testValue, $numbers]) {
        if (isValidEquation($testValue, $numbers)) {
            $calibrationResult += $testValue;
        }
    }
    return $calibrationResult;
}

function isValidEquationPart2($testValue, $numbers): bool
{
    $operators = ['+', '*', '||'];
    $n = count($numbers);

    $stack = [[$numbers[0]]];
    for ($i = 1; $i < $n; $i++) {
        $newStack = [];
        foreach ($stack as $expression) {
            foreach ($operators as $op) {
                $newExpression = [];
                foreach ($expression as $j => $value) {
                    if ($j < count($expression) - 1) {
                        $newExpression[] = $value;
                    } else {
                        $newExpression[] = evaluate($value, $op, $numbers[$i]);
                    }
                }
                $newStack[] = $newExpression;
            }
        }
        $stack = $newStack;
    }

    $results = [];
    foreach ($stack as $expression) {
        $result = $expression[0];
        foreach ($expression as $j => $value) {
            if ($j > 0) {
                $result = evaluate($result, '+', $value);
            }
        }
        $results[] = $result;
    }

    return in_array($testValue, $results);
}

function evaluate($left, $op, $right): float|false|int
{
    $left = (int)$left;
    $right = (int)$right;

    switch ($op) {
        case '+':
            return $left + $right;
        case '*':
            return $left * $right;
        case '||':
            return (int)($left . $right);
        default:
            return false;
    }
}

function solvePart2($input): int
{
    $equations = parseEquations($input);
    $calibrationResult = 0;
    foreach ($equations as [$testValue, $numbers]) {
        if (isValidEquationPart2($testValue, $numbers)) {
            $calibrationResult += $testValue;
        }
    }
    return $calibrationResult;
}

// Part 1

$profiler = new Profiler('Part 1');
$profiler->startProfile();
$resultPart1 = solvePart1($input);
$profiler->stopProfile();
echo "Calibration result: {$resultPart1}" . PHP_EOL;
$profiler->reportProfile();

// Part 2

$profiler = new Profiler('Part 2');
$profiler->startProfile();
$resultPart2 = solvePart2($input);
$profiler->stopProfile();
echo "Calibration result: {$resultPart2}" . PHP_EOL;
$profiler->reportProfile();