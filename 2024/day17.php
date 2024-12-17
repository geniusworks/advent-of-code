<?php
/**
 * Advent of Code 2024
 * Day 17: Chronospatial Computer
 *
 * @author Martin Diekhoff
 * @link https://adventofcode.com/2024/day/17
 */

const DATA_INPUT_FILE = 'input17.txt';

require_once __DIR__ . '/../' . 'bootstrap.php';

$input = DataImporter::importFromFileWithDefaultFlags(__DIR__ . '/' . DATA_INPUT_FILE);

function run($a, $b, $c, array $program): array
{
    $combo = function ($operand) use (&$a, &$b, &$c) {
        return match ($operand) {
            0, 1, 2, 3 => $operand,
            4 => $a,
            5 => $b,
            6 => $c,
            default => 7
        };
    };

    $i = 0;
    $output = [];
    while ($i < count($program)) {
        $op = $program[$i];
        $operand = $program[$i + 1];
        switch ($op) {
            case 0:
                $a = intdiv($a, pow(2, $combo($operand)));
                break;
            case 1:
                $b = $b ^ $operand;
                break;
            case 2:
                $b = $combo($operand) % 8;
                break;
            case 3:
                if ($a == 0) {
                    break;
                }
                $i = $operand;
                continue 2;
            case 4:
                $b = $b ^ $c;
                break;
            case 5:
                $output[] = $combo($operand) % 8;
                break;
            case 6:
                $b = intdiv($a, pow(2, $combo($operand)));
                break;
            case 7:
                $c = intdiv($a, pow(2, $combo($operand)));
                break;
        }
        $i += 2;
    }
    return $output;
}

function f($a, $n, array $program): bool|int
{
    if ($n > count($program)) {
        return $a;
    }
    for ($i = 0; $i < 8; $i++) {
        $_a = $a << 3 | $i;
        $output = run($_a, 0, 0, $program);
        if ($output == array_slice($program, -$n)) {
            if (($result = f($_a, $n + 1, $program)) !== false) {
                return $result;
            }
        }
    }
    return false;
}

$program = array_map('intval', preg_match_all("/\d+/", implode('', $input), $matches) ? $matches[0] : []);

$a = array_shift($program);
$b = array_shift($program);
$c = array_shift($program);

// Part 1

$profiler = new Profiler();
$profiler->startProfile();
$result1 = "Computed output string: " . implode(",", run($a, $b, $c, $program));
$profiler->stopProfile();
echo "Result: {$result1}" . PHP_EOL;
$profiler->reportProfile();

// Part 2

$profiler = new Profiler();
$profiler->startProfile();
$result2 = null; // TODO: Calculate the result for part 2.
$profiler->stopProfile();
echo "Result: {$result2}" . PHP_EOL;
$profiler->reportProfile();