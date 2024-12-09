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

function compactDisk($diskMap): float|int
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

// Part 1

$profiler = new Profiler('Part 1');
$profiler->startProfile();
$result1 = compactDisk($input);
$profiler->stopProfile();
echo "Disk blocks checksum: {$result1}" . PHP_EOL;
$profiler->reportProfile();
