<?php
/**
 * Advent of Code 2024
 * Day 9: Disk Fragmenter
 *
 * @author Martin Diekhoff
 * @link https://adventofcode.com/2024/day/9
 */

const DATA_INPUT_FILE = 'input09.txt';

require_once __DIR__ . '/../' . 'bootstrap.php';

$input = DataImporter::importFromFileContents(__DIR__ . '/' . DATA_INPUT_FILE);

function compactDiskPart1($diskMap): float|int
{
    $blocks = [];
    $fileIndex = 0;
    $blockIndex = 0;
    $i = 0;

    // Process disk map
    while ($i < strlen($diskMap)) {
        [$fileLength, $freeSpaceLength] = processDiskMapEntry($diskMap, $i);
        $i += 2;

        // Add file blocks
        if ($fileLength > 0) {
            for ($j = 0; $j < $fileLength; $j++) {
                $blocks[$blockIndex] = $fileIndex;
                $blockIndex++;
            }
        }
        $fileIndex++;

        // Add free space blocks
        if ($freeSpaceLength > 0) {
            for ($j = 0; $j < $freeSpaceLength; $j++) {
                $blocks[$blockIndex] = '.';
                $blockIndex++;
            }
        }
    }

    // Compact files
    $blocks = compactFilesPart1($blocks);

    // Calculate filesystem checksum
    return calculateChecksum($blocks);
}

function compactFilesPart1($blocks): array
{
    $last_free_space = 0;
    for ($i = count($blocks) - 1; $i >= 0; $i--) {
        $block = $blocks[$i];
        if ($block !== '.') {
            // Find a free space to move the block to
            for ($j = $last_free_space; $j < $i; $j++) {
                if ($blocks[$j] === '.') {
                    // Move the block to the free space
                    $blocks[$j] = $block;
                    $blocks[$i] = '.';
                    $last_free_space = $j;
                    break;
                }
            }
        }
    }

    return $blocks;
}

function calculateChecksumPart1($blocks): float|int
{
    $checksum = 0;
    foreach ($blocks as $index => $block) {
        if ($block !== '.') {
            $checksum += $index * $block;
        }
    }

    return $checksum;
}

function insertIntoSortedNumericArray(&$array, $key, $value): void
{
    $pos = 0;
    foreach ($array as $k => $_) {
        if ($k > $key) {
            break;
        }
        $pos++;
    }
    $array = array_slice($array, 0, $pos, true) + [$key => $value] + array_slice($array, $pos, null, true);
}

function compactDiskPart2($diskMap): float|int
{
    $fileIndex = 0;
    $blockIndex = 0;
    $files = [];
    $free = [];
    $i = 0;

    // Process disk map
    while ($i < strlen($diskMap)) {
        [$fileLength, $freeSpaceLength] = processDiskMapEntry($diskMap, $i);
        $i += 2;

        // Add file blocks
        if ($fileLength > 0) {
            $files[$blockIndex] = array_fill(0, $fileLength, $fileIndex);
        }
        $blockIndex += $fileLength;
        $fileIndex++;

        // Add free space blocks
        if ($freeSpaceLength > 0) {
            $free[$blockIndex] = array_fill(0, $freeSpaceLength, '.');
        }
        $blockIndex += $freeSpaceLength;
    }

    // Compact files
    $blocks = compactFilesPart2($files, $free);

    // Calculate filesystem checksum
    return calculateChecksum($blocks);
}

function processDiskMapEntry($diskMap, $i): array
{
    if ($i + 1 >= strlen($diskMap)) {
        return [(int)$diskMap[$i], 0];
    }

    return [(int)$diskMap[$i], (int)$diskMap[$i + 1]];
}

function compactFilesPart2($files, $free): array
{
    foreach (array_reverse($files, true) as $fileKey => $file) {
        $fileLength = count($file);
        foreach ($free as $freeKey => $freeBlock) {
            if ($freeKey > $fileKey) {
                break;
            }
            $freeLength = count($freeBlock);
            if ($freeLength >= $fileLength) {
                unset($files[$fileKey]);
                $files[$freeKey] = $file;
                unset($free[$freeKey]);
                $free[$fileKey] = array_fill(0, $fileLength, '.');
                if ($freeLength > $fileLength) {
                    insertIntoSortedNumericArray(
                        $free,
                        $freeKey + $fileLength,
                        array_fill(0, ($freeLength - $fileLength), '.'),
                    );
                }
                break;
            }
        }
    }

    // Reconstitute blocks
    $blocks = $free + $files;
    ksort($blocks);
    return array_merge(...$blocks);
}

function calculateChecksum($blocks): float|int
{
    $checksum = 0;
    foreach ($blocks as $index => $block) {
        if ($block !== '.') {
            $checksum += $index * $block;
        }
    }

    return $checksum;
}

// Part 1

$profiler = new Profiler('Part 1');
$profiler->startProfile();
$result1 = compactDiskPart1($input);
$profiler->stopProfile();
echo "Disk blocks checksum (file blocks): {$result1}" . PHP_EOL;
$profiler->reportProfile();

// Part 2

$profiler = new Profiler('Part 2');
$profiler->startProfile();
$result1 = compactDiskPart2($input);
$profiler->stopProfile();
echo PHP_EOL . "Disk blocks checksum (whole files): {$result1}" . PHP_EOL;
$profiler->reportProfile();