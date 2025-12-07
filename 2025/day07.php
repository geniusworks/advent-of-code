<?php
/**
 * Advent of Code 2025
 * Day 7: Laboratories
 *
 * @author Martin Diekhoff
 * @link https://adventofcode.com/2025/day/7
 */

const DATA_INPUT_FILE = 'input07.txt';

require_once __DIR__ . '/../bootstrap.php';

$input = DataImporter::importFromFileWithDefaultFlags(__DIR__ . '/' . DATA_INPUT_FILE);

function solvePart1(array $input)
{
    $lines = [];

    foreach ($input as $line) {
        $lines[] = rtrim($line, "\r\n");
    }

    $rows = count($lines);
    if ($rows === 0) {
        return 0;
    }

    $cols = 0;
    foreach ($lines as $line) {
        $len = strlen($line);
        if ($len > $cols) {
            $cols = $len;
        }
    }

    if ($cols === 0) {
        return 0;
    }

    $startRow = -1;
    $startCol = -1;

    for ($r = 0; $r < $rows; $r++) {
        $len = strlen($lines[$r]);
        for ($c = 0; $c < $len; $c++) {
            if ($lines[$r][$c] === 'S') {
                $startRow = $r;
                $startCol = $c;
                break 2;
            }
        }
    }

    if ($startRow === -1) {
        return 0;
    }

    $startRow++;
    if ($startRow >= $rows) {
        return 0;
    }

    $current = array_fill(0, $cols, false);
    $current[$startCol] = true;

    $splits = 0;

    for ($r = $startRow; $r < $rows; $r++) {
        $next = array_fill(0, $cols, false);
        $line = $lines[$r];
        $lineLen = strlen($line);

        for ($c = 0; $c < $cols; $c++) {
            if (!$current[$c]) {
                continue;
            }

            $ch = ($c < $lineLen) ? $line[$c] : '.';

            if ($ch === '^') {
                $splits++;

                if ($c > 0) {
                    $next[$c - 1] = true;
                }
                if ($c + 1 < $cols) {
                    $next[$c + 1] = true;
                }
            } else {
                $next[$c] = true;
            }
        }

        $current = $next;
    }

    return $splits;
}

function solvePart2(array $input)
{
    $lines = [];

    foreach ($input as $line) {
        $lines[] = rtrim($line, "\r\n");
    }

    $rows = count($lines);
    if ($rows === 0) {
        return 0;
    }

    $cols = 0;
    foreach ($lines as $line) {
        $len = strlen($line);
        if ($len > $cols) {
            $cols = $len;
        }
    }

    if ($cols === 0) {
        return 0;
    }

    $startRow = -1;
    $startCol = -1;

    for ($r = 0; $r < $rows; $r++) {
        $len = strlen($lines[$r]);
        for ($c = 0; $c < $len; $c++) {
            if ($lines[$r][$c] === 'S') {
                $startRow = $r;
                $startCol = $c;
                break 2;
            }
        }
    }

    if ($startRow === -1) {
        return 0;
    }

    $startRow++;
    if ($startRow >= $rows) {
        return 1;
    }

    $current = array_fill(0, $cols, 0);
    $current[$startCol] = 1;

    for ($r = $startRow; $r < $rows; $r++) {
        $next = array_fill(0, $cols, 0);
        $line = $lines[$r];
        $lineLen = strlen($line);

        for ($c = 0; $c < $cols; $c++) {
            $count = $current[$c];
            if ($count === 0) {
                continue;
            }

            $ch = ($c < $lineLen) ? $line[$c] : '.';

            if ($ch === '^') {
                if ($c > 0) {
                    $next[$c - 1] += $count;
                }
                if ($c + 1 < $cols) {
                    $next[$c + 1] += $count;
                }
            } else {
                $next[$c] += $count;
            }
        }

        $current = $next;
    }

    return array_sum($current);
}

$profiler = new Profiler();
$profiler->startProfile();
$result1 = solvePart1($input);
$profiler->stopProfile();
echo "Result: {$result1}" . PHP_EOL;
$profiler->reportProfile();

$profiler = new Profiler();
$profiler->startProfile();
$result2 = solvePart2($input);
$profiler->stopProfile();
echo "Result: {$result2}" . PHP_EOL;
$profiler->reportProfile();
