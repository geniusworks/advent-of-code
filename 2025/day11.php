<?php
/**
 * Advent of Code 2025
 * Day 11: Reactor
 *
 * @author Martin Diekhoff
 * @link https://adventofcode.com/2025/day/11
 */

const DATA_INPUT_FILE = 'input11.txt';

require_once __DIR__ . '/../bootstrap.php';

$input = DataImporter::importFromFileWithDefaultFlags(__DIR__ . '/' . DATA_INPUT_FILE);

function solvePart1(array $input)
{
    $graph = [];

    foreach ($input as $line) {
        $line = trim($line);
        if ($line === '') {
            continue;
        }

        $parts = explode(':', $line, 2);
        if (count($parts) !== 2) {
            continue;
        }

        $from = trim($parts[0]);
        $rest = trim($parts[1]);
        if ($rest === '') {
            $graph[$from] = [];
            continue;
        }

        $to = preg_split('/\s+/', $rest);
        $targets = [];
        foreach ($to as $t) {
            $t = trim($t);
            if ($t !== '') {
                $targets[] = $t;
            }
        }

        if (!isset($graph[$from])) {
            $graph[$from] = $targets;
        } else {
            $graph[$from] = array_merge($graph[$from], $targets);
        }
    }

    $start = 'you';
    $end = 'out';

    $memo = [];
    $visiting = [];

    $countPaths = function ($node) use (&$countPaths, &$graph, &$memo, &$visiting, $end) {
        if ($node === $end) {
            return 1;
        }

        if (isset($memo[$node])) {
            return $memo[$node];
        }

        if (isset($visiting[$node])) {
            return 0;
        }

        $visiting[$node] = true;

        $total = 0;
        if (isset($graph[$node])) {
            foreach ($graph[$node] as $next) {
                $total += $countPaths($next);
            }
        }

        unset($visiting[$node]);
        $memo[$node] = $total;

        return $total;
    };

    return $countPaths($start);
}

function solvePart2(array $input)
{
    $graph = [];

    foreach ($input as $line) {
        $line = trim($line);
        if ($line === '') {
            continue;
        }

        $parts = explode(':', $line, 2);
        if (count($parts) !== 2) {
            continue;
        }

        $from = trim($parts[0]);
        $rest = trim($parts[1]);
        if ($rest === '') {
            $graph[$from] = [];
            continue;
        }

        $to = preg_split('/\s+/', $rest);
        $targets = [];
        foreach ($to as $t) {
            $t = trim($t);
            if ($t !== '') {
                $targets[] = $t;
            }
        }

        if (!isset($graph[$from])) {
            $graph[$from] = $targets;
        } else {
            $graph[$from] = array_merge($graph[$from], $targets);
        }
    }

    $start = 'svr';
    $end = 'out';
    $dac = 'dac';
    $fft = 'fft';

    $memo = [];

    $countPaths = function ($node, bool $seenDac, bool $seenFft) use (&$countPaths, &$graph, &$memo, $end, $dac, $fft) {
        if ($node === $dac) {
            $seenDac = true;
        }
        if ($node === $fft) {
            $seenFft = true;
        }

        $key = $node . '|' . ($seenDac ? '1' : '0') . ($seenFft ? '1' : '0');
        if (isset($memo[$key])) {
            return $memo[$key];
        }

        if ($node === $end) {
            $memo[$key] = ($seenDac && $seenFft) ? 1 : 0;
            return $memo[$key];
        }

        $total = 0;
        if (isset($graph[$node])) {
            foreach ($graph[$node] as $next) {
                $total += $countPaths($next, $seenDac, $seenFft);
            }
        }

        $memo[$key] = $total;
        return $total;
    };

    return $countPaths($start, false, false);
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
