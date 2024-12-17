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

function getCombo(int $operand, int $a, int $b, int $c): int
{
    return match ($operand) {
        0, 1, 2, 3 => $operand,
        4 => $a,
        5 => $b,
        6 => $c,
        default => 7
    };
}

function find_register_a(array $program, int $p, int $r): int|bool
{
    // Base case: if we've matched all digits of the program
    if ($p < 0) {
        return $r;
    }

    // Try all 3-bit digits (0-7)
    for ($d = 0; $d < 8; $d++) {
        // Construct new A value by shifting and adding current digit
        $a = ($r << 3) | $d;
        $b = 0;
        $c = 0;
        $i = 0;
        $w = null;

        // Run the program
        while ($i < count($program)) {
            // Ensure we have both opcode and operand
            if (!isset($program[$i]) || !isset($program[$i + 1])) {
                break;
            }

            // Combo operand logic
            $o = getCombo($program[$i + 1], $a, $b, $c);

            // Instruction processing
            switch ($program[$i]) {
                case 0: // adv
                    $a = intdiv($a, pow(2, $o));
                    break;
                case 1: // bxl
                    $b ^= $program[$i + 1];
                    break;
                case 2: // bst
                    $b = $o & 7;
                    break;
                case 3: // jnz
                    if ($a != 0) {
                        $i = $program[$i + 1] - 2;
                        continue 2;
                    }
                    break;
                case 4: // bxc
                    $b ^= $c;
                    break;
                case 5: // out
                    $w = $o & 7;
                    break;
                case 6: // bdv
                    $b = intdiv($a, pow(2, $o));
                    break;
                case 7: // cdv
                    $c = intdiv($a, pow(2, $o));
                    break;
            }
            $i += 2;
        }

        // Check if the last output matches the current program digit
        if ($w !== null && $w === $program[$p]) {
            $result = find_register_a($program, $p - 1, ($r << 3) | $d);
            if ($result !== false) {
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
$result2 = "Lowest register A value to clone result: " . find_register_a($program, count($program) - 1, 0);
$profiler->stopProfile();
echo "Result: {$result2}" . PHP_EOL;
$profiler->reportProfile();