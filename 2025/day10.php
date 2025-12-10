<?php
/**
 * Advent of Code 2025
 * Day 10: Factory
 *
 * @author Martin Diekhoff
 * @link https://adventofcode.com/2025/day/10
 */

const DATA_INPUT_FILE = 'input10.txt';

require_once __DIR__ . '/../bootstrap.php';

$input = DataImporter::importFromFileWithDefaultFlags(__DIR__ . '/' . DATA_INPUT_FILE);

function bitCount(int $x): int
{
    $count = 0;

    while ($x !== 0) {
        $x &= $x - 1;
        $count++;
    }

    return $count;
}

function solvePart1(array $input)
{
    $total = 0;

    foreach ($input as $line) {
        $line = trim($line);
        if ($line === '') {
            continue;
        }

        if (!preg_match('/\[(.*?)\]/', $line, $m)) {
            continue;
        }

        $pattern = $m[1];
        $len = strlen($pattern);

        $targetMask = 0;

        for ($i = 0; $i < $len; $i++) {
            if ($pattern[$i] === '#') {
                $targetMask |= 1 << $i;
            }
        }

        preg_match_all('/\(([^)]*)\)/', $line, $matches);

        $buttonMasks = [];

        foreach ($matches[1] as $group) {
            $group = trim($group);
            if ($group === '') {
                continue;
            }

            $parts = explode(',', $group);
            $mask = 0;

            foreach ($parts as $p) {
                $p = trim($p);
                if ($p === '') {
                    continue;
                }

                $idx = (int) $p;
                if ($idx >= 0 && $idx < $len) {
                    $mask ^= 1 << $idx;
                }
            }

            $buttonMasks[] = $mask;
        }

        $mButtons = count($buttonMasks);

        if ($mButtons === 0) {
            if ($targetMask !== 0) {
                continue;
            }

            continue;
        }

        $best = null;
        $limit = 1 << $mButtons;

        for ($s = 0; $s < $limit; $s++) {
            if ($best !== null && bitCount($s) >= $best) {
                continue;
            }

            $state = 0;

            for ($j = 0; $j < $mButtons; $j++) {
                if ($s & (1 << $j)) {
                    $state ^= $buttonMasks[$j];
                }
            }

            if ($state === $targetMask) {
                $presses = bitCount($s);

                if ($best === null || $presses < $best) {
                    $best = $presses;
                }

                if ($best === 0) {
                    break;
                }
            }
        }

        if ($best !== null) {
            $total += $best;
        }
    }

    return $total;
}

function solvePart2(array $input)
{
    return null;
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
