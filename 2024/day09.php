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
    for ($i = count($blocks) - 1; $i >= 0; $i--) {
        $block = $blocks[$i];
        if ($block !== '.') {
            for ($j = 0; $j < $i; $j++) {
                if ($blocks[$j] === '.') {
                    $blocks[$j] = $block;
                    $blocks[$i] = '.';
                    break;
                }
            }
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

    $output = $free + $files;
    ksort($output);
    // $mergedArray = array_merge(...$output);
    // echo 'File block (n): ' . implode('', $mergedArray) . PHP_EOL;

    foreach (array_reverse($files, true) as $fileKey => $file) {
        $fileLength = count($file);
        $foundMatch = false;
        foreach ($free as $freeKey => $freeBlock) {
            if ($freeKey > $fileKey) {
                break;
            }
            $freeLength = count($freeBlock);
            if ($freeLength >= $fileLength) {
                $foundMatch = true;
                unset($files[$fileKey]);
                $files[$freeKey] = $file;
                unset($free[$freeKey]);
                $free[$fileKey] = array_fill(0, $fileLength, '.');
                if ($freeLength > $fileLength) {
                    $free[$freeKey + $fileLength] = array_fill(0, ($freeLength - $fileLength), '.');
                    ksort($free);
                }
                break;
            }
        }
        if ($foundMatch) {
            $output = $free + $files;
            ksort($output);
            $mergedArray = [];
            foreach ($output as $value) {
                $mergedArray = array_merge($mergedArray, is_array($value) ? $value : [$value]);
            }
            // echo "File block ({$file[0]}): " . implode('', array_map(function($x) { return ($x == '.') ? '.' : $x; }, $mergedArray)) . PHP_EOL;
        }
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

$profiler = new Profiler('Part 1');
$profiler->startProfile();
$result1 = compactDiskPart2($input);
$profiler->stopProfile();
echo "Disk blocks checksum (whole files): {$result1}" . PHP_EOL;
$profiler->reportProfile();

