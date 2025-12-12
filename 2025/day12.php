<?php
/**
 * Advent of Code 2025
 * Day 12: Christmas Tree Farm
 *
 * @author Martin Diekhoff
 * @link https://adventofcode.com/2025/day/12
 */

const DATA_INPUT_FILE = 'input12.txt';

require_once __DIR__ . '/../bootstrap.php';

$input = DataImporter::importFromFileWithDefaultFlags(__DIR__ . '/' . DATA_INPUT_FILE);

function parseInput(array $input): array
{
    $shapes = [];
    $regions = [];
    $currentShape = null;
    $currentShapeLines = [];

    foreach ($input as $line) {
        $line = rtrim($line, "\r\n");

        if (preg_match('/^(\d+):$/', $line, $m)) {
            if ($currentShape !== null && !empty($currentShapeLines)) {
                $shapes[$currentShape] = $currentShapeLines;
            }
            $currentShape = (int)$m[1];
            $currentShapeLines = [];
            continue;
        }

        if ($currentShape !== null && preg_match('/^[#.]+$/', $line)) {
            $currentShapeLines[] = $line;
            continue;
        }

        if (preg_match('/^(\d+)x(\d+):\s*(.*)$/', $line, $m)) {
            if ($currentShape !== null && !empty($currentShapeLines)) {
                $shapes[$currentShape] = $currentShapeLines;
                $currentShape = null;
                $currentShapeLines = [];
            }

            $width = (int)$m[1];
            $height = (int)$m[2];
            $quantities = array_map('intval', preg_split('/\s+/', trim($m[3])));
            $regions[] = [$width, $height, $quantities];
            continue;
        }

        if ($line === '' && $currentShape !== null && !empty($currentShapeLines)) {
            $shapes[$currentShape] = $currentShapeLines;
            $currentShape = null;
            $currentShapeLines = [];
        }
    }

    if ($currentShape !== null && !empty($currentShapeLines)) {
        $shapes[$currentShape] = $currentShapeLines;
    }

    return [$shapes, $regions];
}

function solvePart1(array $input)
{
    [$shapes, $regions] = parseInput($input);

    $shapeSizes = [];
    foreach ($shapes as $idx => $lines) {
        $shapeSizes[$idx] = substr_count(implode('', $lines), '#');
    }

    $count = 0;
    foreach ($regions as [$width, $height, $quantities]) {
        $totalCells = 0;
        foreach ($quantities as $i => $qty) {
            if ($qty > 0 && isset($shapeSizes[$i])) {
                $totalCells += $qty * $shapeSizes[$i];
            }
        }

        if ($totalCells <= $width * $height) {
            $count++;
        }
    }

    return $count;
}

$profiler = new Profiler();
$profiler->startProfile();
$result1 = solvePart1($input);
$profiler->stopProfile();
echo "Result: {$result1}" . PHP_EOL;
$profiler->reportProfile();
