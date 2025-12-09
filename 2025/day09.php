<?php
/**
 * Advent of Code 2025
 * Day 9: Movie Theater
 */

const DATA_INPUT_FILE = 'input09.txt';

require_once __DIR__ . '/../bootstrap.php';

$input = DataImporter::importFromFileWithDefaultFlags(__DIR__ . '/' . DATA_INPUT_FILE);

function solvePart1(array $input)
{
    $points = [];

    foreach ($input as $line) {
        $line = trim($line);
        if ($line === '') {
            continue;
        }

        $parts = explode(',', $line);
        if (count($parts) !== 2) {
            continue;
        }

        $x = (int) $parts[0];
        $y = (int) $parts[1];

        $points[] = [$x, $y];
    }

    $n = count($points);
    if ($n < 2) {
        return 0;
    }

    $maxArea = 0;

    for ($i = 0; $i < $n; $i++) {
        $pi = $points[$i];
        for ($j = $i + 1; $j < $n; $j++) {
            $pj = $points[$j];

            $width = abs($pi[0] - $pj[0]) + 1;
            $height = abs($pi[1] - $pj[1]) + 1;

            $area = $width * $height;
            if ($area > $maxArea) {
                $maxArea = $area;
            }
        }
    }

    return $maxArea;
}

function solvePart2(array $input)
{
    $redTiles = [];

    foreach ($input as $line) {
        $line = trim($line);
        if ($line === '') {
            continue;
        }

        $parts = explode(',', $line);
        if (count($parts) !== 2) {
            continue;
        }

        $x = (int) $parts[0];
        $y = (int) $parts[1];

        $redTiles[] = [$x, $y];
    }

    $n = count($redTiles);
    if ($n < 2) {
        return 0;
    }

    $allX = [];
    $allY = [];

    foreach ($redTiles as $tile) {
        $allX[$tile[0]] = true;
        $allY[$tile[1]] = true;
    }

    for ($i = 0; $i < $n; $i++) {
        $curr = $redTiles[$i];
        $next = $redTiles[($i + 1) % $n];

        $allX[$curr[0]] = true;
        $allX[$next[0]] = true;
        $allY[$curr[1]] = true;
        $allY[$next[1]] = true;
    }

    $xCoords = array_keys($allX);
    $yCoords = array_keys($allY);
    sort($xCoords);
    sort($yCoords);

    $xToIdx = array_flip($xCoords);
    $yToIdx = array_flip($yCoords);

    $numX = count($xCoords);
    $numY = count($yCoords);

    $verticalSegments = [];

    for ($i = 0; $i < $n; $i++) {
        $curr = $redTiles[$i];
        $next = $redTiles[($i + 1) % $n];

        if ($curr[0] === $next[0]) {
            $x = $curr[0];
            $y1 = min($curr[1], $next[1]);
            $y2 = max($curr[1], $next[1]);

            if (!isset($verticalSegments[$x])) {
                $verticalSegments[$x] = [];
            }

            $verticalSegments[$x][] = [$y1, $y2];
        }
    }

    $filled = [];

    $redSet = [];
    foreach ($redTiles as $tile) {
        $redSet[$tile[0] . ',' . $tile[1]] = true;
    }

    foreach ($xCoords as $x) {
        foreach ($yCoords as $y) {

            $crossings = 0;

            foreach ($verticalSegments as $segX => $segments) {
                if ($segX >= $x) {
                    continue;
                }

                foreach ($segments as $seg) {
                    if ($y >= $seg[0] && $y < $seg[1]) {
                        $crossings++;
                    }
                }
            }

            if ($crossings % 2 === 1) {
                $filled[$x . ',' . $y] = true;
            }
        }
    }

    for ($i = 0; $i < $n; $i++) {
        $curr = $redTiles[$i];
        $next = $redTiles[($i + 1) % $n];

        if ($curr[0] === $next[0]) {
            $x = $curr[0];
            $y1 = min($curr[1], $next[1]);
            $y2 = max($curr[1], $next[1]);

            foreach ($yCoords as $y) {
                if ($y >= $y1 && $y <= $y2) {
                    $filled[$x . ',' . $y] = true;
                }
            }
        } else {
            $y = $curr[1];
            $x1 = min($curr[0], $next[0]);
            $x2 = max($curr[0], $next[0]);

            foreach ($xCoords as $x) {
                if ($x >= $x1 && $x <= $x2) {
                    $filled[$x . ',' . $y] = true;
                }
            }
        }
    }

    $maxArea = 0;

    for ($i = 0; $i < $n; $i++) {
        $pi = $redTiles[$i];
        for ($j = $i + 1; $j < $n; $j++) {
            $pj = $redTiles[$j];

            $minX = min($pi[0], $pj[0]);
            $maxX = max($pi[0], $pj[0]);
            $minY = min($pi[1], $pj[1]);
            $maxY = max($pi[1], $pj[1]);

            $valid = true;

            foreach ($xCoords as $x) {
                if ($x < $minX || $x > $maxX) {
                    continue;
                }

                foreach ($yCoords as $y) {
                    if ($y < $minY || $y > $maxY) {
                        continue;
                    }

                    if (!isset($filled[$x . ',' . $y])) {
                        $valid = false;
                        break 2;
                    }
                }
            }

            if ($valid) {
                $width = $maxX - $minX + 1;
                $height = $maxY - $minY + 1;
                $area = $width * $height;

                if ($area > $maxArea) {
                    $maxArea = $area;
                }
            }
        }
    }

    return $maxArea;
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
