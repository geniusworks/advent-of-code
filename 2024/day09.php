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
    $fileId = 0;
    $i = 0;
    while ($i < strlen($diskMap)) {
        if ($i + 1 >= strlen($diskMap)) {
            $fileLength = (int)$diskMap[$i];
            for ($j = 0; $j < $fileLength; $j++) {
                $blocks[] = $fileId;
            }
            break;
        }

        $fileLength = (int)$diskMap[$i];
        $freeSpaceLength = (int)$diskMap[$i + 1];

        // Add file blocks for current file ID
        for ($j = 0; $j < $fileLength; $j++) {
            $blocks[] = $fileId;
        }

        // Add free space blocks
        for ($j = 0; $j < $freeSpaceLength; $j++) {
            $blocks[] = '.';
        }

        $fileId++;
        $i += 2;
    }

    // Compact files
    $last_update_time = microtime(true);
    $block_count = count($blocks);
    $last_free_space = 0;

    for ($i = $block_count - 1; $i >= 0; $i--) {
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

        $progress = ($block_count - $i) / $block_count * 100;
        $current_time = microtime(true);

        if ($current_time - $last_update_time >= 1) {
            echo "Progress: " . intval($progress) . "%\n";
            $last_update_time = $current_time;
        }
    }

    // Calculate filesystem checksum
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
    while ($i < strlen($diskMap)) {
        if ($i + 1 >= strlen($diskMap)) {
            $fileLength = (int)$diskMap[$i];
            $files[$blockIndex] = array_fill(0, $fileLength, $fileIndex);
            break;
        }

        $fileLength = (int)$diskMap[$i];
        $freeSpaceLength = (int)$diskMap[$i + 1];

        if ($fileLength > 0) {
            $files[$blockIndex] = array_fill(0, $fileLength, $fileIndex);
        }
        $blockIndex += $fileLength;
        $fileIndex++;

        if ($freeSpaceLength > 0) {
            $free[$blockIndex] = array_fill(0, $freeSpaceLength, '.');
        }
        $blockIndex += $freeSpaceLength;

        $i += 2;
    }

    // Compact files
    $last_update_time = microtime(true);
    $files_count = count($files);
    $files_counter = 0;

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

        $current_time = microtime(true);

        if ($current_time - $last_update_time >= 1) {
            $progress = ($files_count - $files_counter) / $files_count * 100;
            echo "Progress: " . intval($progress) . "%\n";
            $last_update_time = $current_time;
        }

        $files_counter++;
    }

    // Reconstitute blocks
    $blocks = $free + $files;
    ksort($blocks);
    $blocks = array_merge(...$blocks);

    // Calculate filesystem checksum
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