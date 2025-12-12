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
    $shapeId = null;
    $shapeLines = [];

    foreach ($input as $line) {
        $line = rtrim($line, "\r\n");

        if (preg_match('/^(\d+):$/', $line, $m)) {
            if ($shapeId !== null) $shapes[$shapeId] = $shapeLines;
            $shapeId = (int)$m[1];
            $shapeLines = [];
        } elseif ($shapeId !== null && preg_match('/^[#.]+$/', $line)) {
            $shapeLines[] = $line;
        } elseif (preg_match('/^(\d+)x(\d+):\s*(.*)$/', $line, $m)) {
            if ($shapeId !== null) {
                $shapes[$shapeId] = $shapeLines;
                $shapeId = null;
            }
            $regions[] = [(int)$m[1], (int)$m[2], array_map('intval', preg_split('/\s+/', trim($m[3])))];
        } elseif ($line === '' && $shapeId !== null) {
            $shapes[$shapeId] = $shapeLines;
            $shapeId = null;
        }
    }

    if ($shapeId !== null) $shapes[$shapeId] = $shapeLines;
    return [$shapes, $regions];
}

function requiredCells(array $q, array $sizes): int
{
    $total = 0;
    foreach ($q as $i => $n) {
        if ($n > 0 && isset($sizes[$i])) $total += $n * $sizes[$i];
    }
    return $total;
}

function normalizeCoords(array $coords): array
{
    if (!$coords) return [];

    $minX = $minY = PHP_INT_MAX;
    foreach ($coords as [$x, $y]) {
        if ($x < $minX) $minX = $x;
        if ($y < $minY) $minY = $y;
    }

    $out = [];
    foreach ($coords as [$x, $y]) {
        $out[] = [$x - $minX, $y - $minY];
    }

    usort($out, fn($a, $b) => $a[1] <=> $b[1] ?: $a[0] <=> $b[0]);
    return $out;
}

function getOrientations(array $lines): array
{
    $coords = [];
    foreach ($lines as $y => $line) {
        for ($x = 0, $len = strlen($line); $x < $len; $x++) {
            if ($line[$x] === '#') $coords[] = [$x, $y];
        }
    }
    $coords = normalizeCoords($coords);

    $seen = [];
    $orientations = [];
    $cur = $coords;

    for ($r = 0; $r < 4; $r++) {
        $flip = [];
        foreach ($cur as [$x, $y]) $flip[] = [-$x, $y];
        $flip = normalizeCoords($flip);

        foreach ([$cur, $flip] as $v) {
            $key = json_encode($v);
            if (!isset($seen[$key])) {
                $seen[$key] = true;
                $maxX = $maxY = 0;
                foreach ($v as [$x, $y]) {
                    if ($x > $maxX) $maxX = $x;
                    if ($y > $maxY) $maxY = $y;
                }
                $orientations[] = ['cells' => $v, 'maxX' => $maxX, 'maxY' => $maxY];
            }
        }

        $rot = [];
        foreach ($cur as [$x, $y]) $rot[] = [$y, -$x];
        $cur = normalizeCoords($rot);
    }

    return $orientations;
}

function buildPlacements(int $W, int $H, array $orientsByType, array $counts): array
{
    $result = [];

    foreach ($orientsByType as $type => $orients) {
        if (($counts[$type] ?? 0) <= 0) continue;

        $seen = [];
        $placements = [];

        foreach ($orients as ['cells' => $cells, 'maxX' => $maxX, 'maxY' => $maxY]) {
            for ($oy = 0; $oy + $maxY < $H; $oy++) {
                for ($ox = 0; $ox + $maxX < $W; $ox++) {
                    $idxs = [];
                    foreach ($cells as [$dx, $dy]) {
                        $idxs[] = ($oy + $dy) * $W + ($ox + $dx);
                    }
                    sort($idxs);
                    $key = implode(',', $idxs);
                    if (!isset($seen[$key])) {
                        $seen[$key] = true;
                        $placements[] = $idxs;
                    }
                }
            }
        }

        $result[$type] = $placements;
    }

    return $result;
}

function greedyPack(int $W, int $H, array $counts, array $orients, array $sizes): bool
{
    $boardSize = $W * $H;
    $occupied = array_fill(0, $boardSize, false);
    $remaining = requiredCells($counts, $sizes);
    $free = $boardSize;
    $scan = 0;

    while ($remaining > 0) {
        if ($remaining > $free) return false;

        $first = -1;
        for ($i = $scan; $i < $boardSize; $i++) {
            if (!$occupied[$i]) { $first = $i; break; }
        }
        if ($first === -1) {
            for ($i = 0; $i < $scan; $i++) {
                if (!$occupied[$i]) { $first = $i; break; }
            }
        }
        if ($first === -1) return false;
        $scan = $first;

        $cx = $first % $W;
        $cy = intdiv($first, $W);

        $types = [];
        foreach ($counts as $t => $n) if ($n > 0) $types[] = $t;
        usort($types, fn($a, $b) => ($sizes[$b] ?? 0) <=> ($sizes[$a] ?? 0) ?: ($counts[$b] ?? 0) <=> ($counts[$a] ?? 0));

        $placed = false;
        foreach ($types as $type) {
            foreach ($orients[$type] ?? [] as ['cells' => $cells, 'maxX' => $maxX, 'maxY' => $maxY]) {
                foreach ($cells as [$ax, $ay]) {
                    $ox = $cx - $ax;
                    $oy = $cy - $ay;
                    if ($ox < 0 || $oy < 0 || $ox + $maxX >= $W || $oy + $maxY >= $H) continue;

                    $base = $oy * $W + $ox;
                    $fits = true;
                    foreach ($cells as [$dx, $dy]) {
                        if ($occupied[$base + $dy * $W + $dx]) { $fits = false; break; }
                    }

                    if ($fits) {
                        foreach ($cells as [$dx, $dy]) $occupied[$base + $dy * $W + $dx] = true;
                        $counts[$type]--;
                        $remaining -= $sizes[$type];
                        $free -= $sizes[$type];
                        $placed = true;
                        break 3;
                    }
                }
            }
        }

        if (!$placed) {
            $occupied[$first] = true;
            $free--;
        }
    }
    return true;
}

function packDfs(array &$occupied, array &$counts, array $placements, array $sizes, int $remaining, int $free): bool
{
    if ($remaining === 0) return true;
    if ($remaining > $free) return false;

    $bestType = null;
    $bestValid = PHP_INT_MAX;

    foreach ($counts as $type => $need) {
        if ($need <= 0) continue;
        if (!isset($placements[$type])) return false;

        $valid = 0;
        foreach ($placements[$type] as $p) {
            $fits = true;
            foreach ($p as $idx) if ($occupied[$idx]) { $fits = false; break; }
            if ($fits && ++$valid >= $bestValid) break;
        }

        if ($valid === 0) return false;
        if ($valid < $bestValid) {
            $bestValid = $valid;
            $bestType = $type;
            if ($bestValid === 1) break;
        }
    }

    if ($bestType === null) return false;

    $counts[$bestType]--;
    $size = $sizes[$bestType];

    foreach ($placements[$bestType] as $p) {
        $fits = true;
        foreach ($p as $idx) if ($occupied[$idx]) { $fits = false; break; }
        if (!$fits) continue;

        foreach ($p as $idx) $occupied[$idx] = true;
        if (packDfs($occupied, $counts, $placements, $sizes, $remaining - $size, $free - $size)) return true;
        foreach ($p as $idx) $occupied[$idx] = false;
    }

    $counts[$bestType]++;
    return false;
}

function part1(array $regions, array $sizes): int
{
    $count = 0;
    foreach ($regions as [$w, $h, $q]) {
        if (requiredCells($q, $sizes) <= $w * $h) $count++;
    }
    return $count;
}

function part2(array $regions, array $sizes, array $orients): int
{
    $count = 0;
    foreach ($regions as [$w, $h, $q]) {
        $need = requiredCells($q, $sizes);
        if ($need > $w * $h) continue;

        if (greedyPack($w, $h, $q, $orients, $sizes)) {
            $count++;
            continue;
        }

        $placements = buildPlacements($w, $h, $orients, $q);
        foreach ($q as $t => $n) {
            if ($n > 0 && (!isset($placements[$t]) || count($placements[$t]) < $n)) continue 2;
        }

        $occupied = array_fill(0, $w * $h, false);
        $counts = $q;
        if (packDfs($occupied, $counts, $placements, $sizes, $need, $w * $h)) $count++;
    }
    return $count;
}

function prepareInput(array $input): array
{
    static $prepared = null;
    if ($prepared !== null) return $prepared;

    [$shapes, $regions] = parseInput($input);
    $shapeSizes = [];
    $orientationMetaByType = [];
    foreach ($shapes as $idx => $lines) {
        $shapeSizes[$idx] = substr_count(implode('', $lines), '#');
        $orientationMetaByType[$idx] = getOrientations($lines);
    }

    return $prepared = [$regions, $shapeSizes, $orientationMetaByType];
}

function solvePart1(array $input): int
{
    [$regions, $shapeSizes] = prepareInput($input);
    return part1($regions, $shapeSizes);
}

function solvePart2(array $input): int
{
    [$regions, $shapeSizes, $orientationMetaByType] = prepareInput($input);
    return part2($regions, $shapeSizes, $orientationMetaByType);
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
