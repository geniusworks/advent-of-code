<?php
/**
 * Advent of Code 2025
 * Day 6: Trash Compactor
 *
 * @author Martin Diekhoff
 * @link https://adventofcode.com/2025/day/6
 */

const DATA_INPUT_FILE = 'input06.txt';

require_once __DIR__ . '/../bootstrap.php';

$input = DataImporter::importFromFileWithDefaultFlags(__DIR__ . '/' . DATA_INPUT_FILE);

function solvePart1(array $input)
{
    $lines = [];

    foreach ($input as $line) {
        $lines[] = rtrim($line, "\r\n");
    }

    $rowCount = count($lines);
    if ($rowCount === 0) {
        return 0;
    }

    $width = 0;
    foreach ($lines as $line) {
        $len = strlen($line);
        if ($len > $width) {
            $width = $len;
        }
    }

    if ($width === 0) {
        return 0;
    }

    $isSeparator = array_fill(0, $width, true);

    for ($c = 0; $c < $width; $c++) {
        for ($r = 0; $r < $rowCount; $r++) {
            $line = $lines[$r];
            $ch = ($c < strlen($line)) ? $line[$c] : ' ';
            if ($ch !== ' ') {
                $isSeparator[$c] = false;
                break;
            }
        }
    }

    $spans = [];
    $inSpan = false;
    $spanStart = 0;

    for ($c = 0; $c <= $width; $c++) {
        $sep = ($c === $width) ? true : $isSeparator[$c];

        if (!$inSpan && !$sep) {
            $inSpan = true;
            $spanStart = $c;
        } elseif ($inSpan && $sep) {
            $spans[] = [$spanStart, $c - 1];
            $inSpan = false;
        }
    }

    if (empty($spans)) {
        return 0;
    }

    $bottomRow = $rowCount - 1;
    $total = 0;

    foreach ($spans as [$start, $end]) {
        $substr = substr($lines[$bottomRow], $start, $end - $start + 1);
        $op = trim($substr);

        if ($op !== '+' && $op !== '*') {
            continue;
        }

        $numbers = [];

        for ($r = 0; $r < $bottomRow; $r++) {
            $segment = substr($lines[$r], $start, $end - $start + 1);
            $segment = trim($segment);
            if ($segment === '') {
                continue;
            }
            $numbers[] = (int) $segment;
        }

        if (empty($numbers)) {
            continue;
        }

        if ($op === '+') {
            $value = array_sum($numbers);
        } else {
            $value = 1;
            foreach ($numbers as $n) {
                $value *= $n;
            }
        }

        $total += $value;
    }

    return $total;
}

function solvePart2(array $input)
{
    $lines = [];

    foreach ($input as $line) {
        $lines[] = rtrim($line, "\r\n");
    }

    $rowCount = count($lines);
    if ($rowCount === 0) {
        return 0;
    }

    $width = 0;
    foreach ($lines as $line) {
        $len = strlen($line);
        if ($len > $width) {
            $width = $len;
        }
    }

    if ($width === 0) {
        return 0;
    }

    $isSeparator = array_fill(0, $width, true);

    for ($c = 0; $c < $width; $c++) {
        for ($r = 0; $r < $rowCount; $r++) {
            $line = $lines[$r];
            $ch = ($c < strlen($line)) ? $line[$c] : ' ';
            if ($ch !== ' ') {
                $isSeparator[$c] = false;
                break;
            }
        }
    }

    $spans = [];
    $inSpan = false;
    $spanStart = 0;

    for ($c = 0; $c <= $width; $c++) {
        $sep = ($c === $width) ? true : $isSeparator[$c];

        if (!$inSpan && !$sep) {
            $inSpan = true;
            $spanStart = $c;
        } elseif ($inSpan && $sep) {
            $spans[] = [$spanStart, $c - 1];
            $inSpan = false;
        }
    }

    if (empty($spans)) {
        return 0;
    }

    $bottomRow = $rowCount - 1;
    $total = 0;

    foreach ($spans as [$start, $end]) {
        $substr = substr($lines[$bottomRow], $start, $end - $start + 1);
        $op = trim($substr);

        if ($op !== '+' && $op !== '*') {
            continue;
        }

        $numbers = [];

        for ($c = $start; $c <= $end; $c++) {
            $digits = '';

            for ($r = 0; $r < $bottomRow; $r++) {
                $line = $lines[$r];
                $ch = ($c < strlen($line)) ? $line[$c] : ' ';

                if ($ch >= '0' && $ch <= '9') {
                    $digits .= $ch;
                }
            }

            if ($digits === '') {
                continue;
            }

            $numbers[] = (int) $digits;
        }

        if (empty($numbers)) {
            continue;
        }

        if ($op === '+') {
            $value = array_sum($numbers);
        } else {
            $value = 1;
            foreach ($numbers as $n) {
                $value *= $n;
            }
        }

        $total += $value;
    }

    return $total;
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
