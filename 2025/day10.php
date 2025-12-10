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
    $total = 0;
    
    foreach ($input as $line) {
        $line = trim($line);
        if ($line === '') continue;

        $parts = explode(' ', $line);
        array_shift($parts);

        $joltageStr = array_pop($parts);
        $joltage = array_map('intval', explode(',', trim($joltageStr, '{}')));
        $m = count($joltage);

        $buttons = [];
        foreach ($parts as $btnStr) {
            $indices = array_map('intval', explode(',', trim($btnStr, '()')));
            $buttons[] = $indices;
        }
        $n = count($buttons);

        $sbuttons = [];
        for ($i = 0; $i < $n; $i++) {
            $row = array_fill(0, $m, 0);
            foreach ($buttons[$i] as $j) {
                if ($j >= 0 && $j < $m) $row[$j] = 1;
            }
            $sbuttons[] = $row;
        }
        
        $result = solveILP($sbuttons, $joltage, $n, $m);
        if ($result === PHP_INT_MAX) return PHP_INT_MAX;
        $total += $result;
    }
    
    return $total;
}

function solveILP(array $A, array $b, int $n, int $m): int
{
    if ($n === 0) {
        return array_sum($b) === 0 ? 0 : PHP_INT_MAX;
    }
    
    $M = [];
    for ($j = 0; $j < $m; $j++) {
        $row = [];
        for ($i = 0; $i < $n; $i++) {
            $row[] = $A[$i][$j];
        }
        $row[] = $b[$j];
        $M[] = $row;
    }
    
    $pivotCol = [];
    $pivotRow = array_fill(0, $n, -1);
    $curRow = 0;
    
    for ($col = 0; $col < $n && $curRow < $m; $col++) {

        $pivotR = -1;
        for ($r = $curRow; $r < $m; $r++) {
            if (abs($M[$r][$col]) > 1e-9) {
                $pivotR = $r;
                break;
            }
        }
        
        if ($pivotR === -1) {
            continue;
        }
        
        if ($pivotR !== $curRow) {
            $tmp = $M[$curRow];
            $M[$curRow] = $M[$pivotR];
            $M[$pivotR] = $tmp;
        }
        
        $pivotCol[$curRow] = $col;
        $pivotRow[$col] = $curRow;
        
        $pivotVal = $M[$curRow][$col];
        for ($c = 0; $c <= $n; $c++) {
            $M[$curRow][$c] /= $pivotVal;
        }
        
        for ($r = 0; $r < $m; $r++) {
            if ($r !== $curRow && abs($M[$r][$col]) > 1e-9) {
                $factor = $M[$r][$col];
                for ($c = 0; $c <= $n; $c++) {
                    $M[$r][$c] -= $factor * $M[$curRow][$c];
                }
            }
        }
        
        $curRow++;
    }
    
    for ($r = 0; $r < $m; $r++) {
        $allZero = true;
        for ($c = 0; $c < $n; $c++) {
            if (abs($M[$r][$c]) > 1e-9) {
                $allZero = false;
                break;
            }
        }
        if ($allZero && abs($M[$r][$n]) > 1e-9) {
            return PHP_INT_MAX;
        }
    }
    
    $freeVars = [];
    $pivotVars = [];
    for ($c = 0; $c < $n; $c++) {
        if ($pivotRow[$c] === -1) {
            $freeVars[] = $c;
        } else {
            $pivotVars[] = $c;
        }
    }
    
    if (empty($freeVars)) {
        $x = array_fill(0, $n, 0);
        foreach ($pivotVars as $c) {
            $r = $pivotRow[$c];
            $val = $M[$r][$n] / $M[$r][$c];
            if ($val < -1e-9 || abs($val - round($val)) > 1e-9) {
                return PHP_INT_MAX;
            }
            $x[$c] = (int)round($val);
        }
        return array_sum($x);
    }
    
    $numFree = count($freeVars);
    
    $freeUpper = [];
    foreach ($freeVars as $c) {
        $u = PHP_INT_MAX;
        for ($j = 0; $j < $m; $j++) {
            if ($A[$c][$j] === 1) $u = min($u, $b[$j]);
        }
        $freeUpper[] = $u === PHP_INT_MAX ? 0 : $u;
    }
    
    $pivotCoeffs = [];
    foreach ($pivotVars as $c) {
        $r = $pivotRow[$c];
        $divisor = $M[$r][$c];
        $const = $M[$r][$n] / $divisor;
        $coeffs = [];
        foreach ($freeVars as $fi => $fc) {
            if (abs($M[$r][$fc]) > 1e-9) {
                $coeffs[$fi] = -$M[$r][$fc] / $divisor;
            }
        }
        $pivotCoeffs[$c] = [$const, $coeffs];
    }
    
    $best = PHP_INT_MAX;
    $freeVals = array_fill(0, $numFree, 0);
    searchFree($freeVars, $freeUpper, $freeVals, $pivotVars, $pivotCoeffs, $numFree, 0, 0, $best);
    
    return $best;
}

function searchFree(array $freeVars, array $freeUpper, array $freeVals, array $pivotVars, array $pivotCoeffs, int $numFree, int $idx, int $freeSum, int &$best): void
{
    if ($freeSum >= $best) return;

    foreach ($pivotVars as $c) {
        [$const, $coeffs] = $pivotCoeffs[$c];
        $val = $const;
        $allNonPos = true;
        foreach ($coeffs as $fi => $coeff) {
            if ($fi < $idx) {
                $val += $coeff * $freeVals[$fi];
            } else {
                if ($coeff > 1e-9) $allNonPos = false;
            }
        }

        if ($val < -1e-9 && $allNonPos) return;
    }

    if ($idx >= $numFree) {

        $pivotSum = 0;

        foreach ($pivotVars as $c) {
            [$const, $coeffs] = $pivotCoeffs[$c];
            $val = $const;
            foreach ($coeffs as $fi => $coeff) {
                $val += $coeff * $freeVals[$fi];
            }

            if ($val < -1e-9 || abs($val - round($val)) > 1e-9) return;
            $pivotSum += (int)round($val);
        }
        
        $total = $freeSum + $pivotSum;
        if ($total < $best) $best = $total;
        return;
    }
    
    for ($v = 0; $v <= $freeUpper[$idx]; $v++) {
        if ($freeSum + $v >= $best) break;
        $freeVals[$idx] = $v;
        searchFree($freeVars, $freeUpper, $freeVals, $pivotVars, $pivotCoeffs, $numFree, $idx + 1, $freeSum + $v, $best);
    }
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
