<?php
/**
 * Advent of Code 2025
 * Day 8: Playground
 *
 * @author Martin Diekhoff
 * @link https://adventofcode.com/2025/day/8
 */

const DATA_INPUT_FILE = 'input08.txt';

require_once __DIR__ . '/../bootstrap.php';

$input = DataImporter::importFromFileWithDefaultFlags(__DIR__ . '/' . DATA_INPUT_FILE);

function findRoot(array &$parent, int $x): int
{
    while ($parent[$x] !== $x) {
        $parent[$x] = $parent[$parent[$x]];
        $x = $parent[$x];
    }

    return $x;
}

function solvePart1(array $input)
{
    $points = [];

    foreach ($input as $line) {
        $line = trim($line);
        if ($line === '') {
            continue;
        }

        $parts = explode(',', $line);
        if (count($parts) !== 3) {
            continue;
        }

        $x = (int) $parts[0];
        $y = (int) $parts[1];
        $z = (int) $parts[2];

        $points[] = [$x, $y, $z];
    }

    $n = count($points);
    if ($n === 0) {
        return 0;
    }

    if ($n === 1) {
        return 1;
    }

    $maxEdges = 1000;
    $heap = new SplPriorityQueue();
    $heap->setExtractFlags(SplPriorityQueue::EXTR_BOTH);

    for ($i = 0; $i < $n; $i++) {
        $pi = $points[$i];
        for ($j = $i + 1; $j < $n; $j++) {
            $pj = $points[$j];

            $dx = $pi[0] - $pj[0];
            $dy = $pi[1] - $pj[1];
            $dz = $pi[2] - $pj[2];

            $dist2 = $dx * $dx + $dy * $dy + $dz * $dz;

            if ($heap->count() < $maxEdges) {
                $heap->insert([$i, $j], $dist2);
            } else {
                $top = $heap->current();
                $currentMax = $top['priority'];

                if ($dist2 < $currentMax) {
                    $heap->extract();
                    $heap->insert([$i, $j], $dist2);
                }
            }
        }
    }

    $bestEdges = [];
    $heapClone = clone $heap;

    foreach ($heapClone as $item) {
        $bestEdges[] = [$item['priority'], $item['data'][0], $item['data'][1]];
    }

    usort($bestEdges, function ($a, $b) {
        if ($a[0] === $b[0]) {
            if ($a[1] === $b[1]) {
                return $a[2] <=> $b[2];
            }

            return $a[1] <=> $b[1];
        }

        return $a[0] <=> $b[0];
    });

    $parent = [];
    $size = [];

    for ($i = 0; $i < $n; $i++) {
        $parent[$i] = $i;
        $size[$i] = 1;
    }

    $edgesCount = count($bestEdges);
    $steps = $edgesCount < 1000 ? $edgesCount : 1000;

    for ($k = 0; $k < $steps; $k++) {
        $edge = $bestEdges[$k];
        $i = $edge[1];
        $j = $edge[2];

        $ri = findRoot($parent, $i);
        $rj = findRoot($parent, $j);

        if ($ri === $rj) {
            continue;
        }

        if ($size[$ri] < $size[$rj]) {
            $tmp = $ri;
            $ri = $rj;
            $rj = $tmp;
        }

        $parent[$rj] = $ri;
        $size[$ri] += $size[$rj];
    }

    $componentSizes = [];

    for ($i = 0; $i < $n; $i++) {
        $root = findRoot($parent, $i);
        if (!isset($componentSizes[$root])) {
            $componentSizes[$root] = 0;
        }

        $componentSizes[$root]++;
    }

    $sizes = array_values($componentSizes);
    rsort($sizes);

    $countSizes = count($sizes);
    if ($countSizes === 0) {
        return 0;
    }

    $limit = $countSizes < 3 ? $countSizes : 3;
    $product = 1;

    for ($i = 0; $i < $limit; $i++) {
        $product *= $sizes[$i];
    }

    return $product;
}

function solvePart2(array $input)
{
    $points = [];

    foreach ($input as $line) {
        $line = trim($line);
        if ($line === '') {
            continue;
        }

        $parts = explode(',', $line);
        if (count($parts) !== 3) {
            continue;
        }

        $x = (int) $parts[0];
        $y = (int) $parts[1];
        $z = (int) $parts[2];

        $points[] = [$x, $y, $z];
    }

    $n = count($points);
    if ($n < 2) {
        return 0;
    }

    $inMst = array_fill(0, $n, false);
    $minDist = array_fill(0, $n, PHP_INT_MAX);
    $parent = array_fill(0, $n, -1);

    $minDist[0] = 0;

    for ($iter = 0; $iter < $n; $iter++) {
        $u = -1;
        $best = PHP_INT_MAX;

        for ($i = 0; $i < $n; $i++) {
            if (!$inMst[$i] && $minDist[$i] < $best) {
                $best = $minDist[$i];
                $u = $i;
            }
        }

        if ($u === -1) {
            break;
        }

        $inMst[$u] = true;

        $pu = $points[$u];
        $ux = $pu[0];
        $uy = $pu[1];
        $uz = $pu[2];

        for ($v = 0; $v < $n; $v++) {
            if ($inMst[$v] || $v === $u) {
                continue;
            }

            $pv = $points[$v];
            $dx = $ux - $pv[0];
            $dy = $uy - $pv[1];
            $dz = $uz - $pv[2];

            $dist2 = $dx * $dx + $dy * $dy + $dz * $dz;

            if ($dist2 < $minDist[$v]) {
                $minDist[$v] = $dist2;
                $parent[$v] = $u;
            }
        }
    }

    $maxDist2 = -1;
    $bestU = 0;
    $bestV = 1;

    for ($v = 1; $v < $n; $v++) {
        $u = $parent[$v];
        if ($u === -1) {
            continue;
        }

        $w = $minDist[$v];
        if ($w > $maxDist2) {
            $maxDist2 = $w;
            $bestU = $u;
            $bestV = $v;
        }
    }

    $x1 = $points[$bestU][0];
    $x2 = $points[$bestV][0];

    return $x1 * $x2;
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
