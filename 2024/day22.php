<?php
/**
 * Advent of Code 2024
 * Day 22: Monkey Market
 *
 * @author Martin Diekhoff
 * @link https://adventofcode.com/2024/day/22
 */

const DATA_INPUT_FILE = 'input22.txt';

require_once __DIR__ . '/../' . 'bootstrap.php';

$input = DataImporter::importFromFileWithDefaultFlags(__DIR__ . '/' . DATA_INPUT_FILE);

function solvePart1($input): int
{
    $sum = 0;

    foreach ($input as $index => $buyer) {
        $secretNumber = (int)$buyer;
        for ($i = 0; $i < 2000; $i++) {
            $secretNumber = ($secretNumber * 64) ^ $secretNumber;
            $secretNumber %= 16777216;

            $temp = (int)($secretNumber / 32);
            $secretNumber = $temp ^ $secretNumber;
            $secretNumber %= 16777216;

            $secretNumber = ($secretNumber * 2048) ^ $secretNumber;
            $secretNumber %= 16777216;
        }
        // echo ($index + 1) . ": " . $secretNumber . "\n";
        $sum += $secretNumber;
    }

    return $sum;
}

function generateNextPrice(int &$secretNumber): int
{
    $price = $secretNumber % 10;

    // Update secret number using XOR and modulo operations
    $secretNumber = (($secretNumber * 64) ^ $secretNumber) % 16777216;

    // Use intdiv to ensure integer division
    $secretNumber = intdiv($secretNumber, 32) ^ $secretNumber;
    $secretNumber %= 16777216;

    $secretNumber = (($secretNumber * 2048) ^ $secretNumber) % 16777216;

    return $price;
}

function printBestSequenceResults(array $input, array $bestSequence, array $bestResults): void
{
    foreach ($input as $index => $buyer) {
        if (isset($bestResults[$index])) {
            echo "For the buyer with initial secret {$buyer}, changes " .
                implode(", ", $bestSequence) .
                " first occur when the price is {$bestResults[$index]}.\n";
        } else {
            echo "For the buyer with initial secret {$buyer}, the change sequence " .
                implode(", ", $bestSequence) .
                " does not occur in the first 2000 changes.\n";
        }
    }

    echo "So, by asking the monkey to sell the first time each buyer's prices go " .
        implode(", ", $bestSequence) . ", you would get " .
        array_sum($bestResults) . " (" . implode(" + ", $bestResults) . ") bananas!\n";
}

function findFirstOccurrence(int $initialSecret, array $targetChanges): array
{
    $secretNumber = $initialSecret;
    $prices = [];

    // Get initial price but don't use it
    generateNextPrice($secretNumber);

    // Get first 5 prices
    for ($i = 0; $i < 5; $i++) {
        $prices[] = generateNextPrice($secretNumber);
    }

    // Check first sequence
    $changes = [];
    for ($i = 0; $i < 4; $i++) {
        $changes[] = $prices[$i + 1] - $prices[$i];
    }
    if ($changes === $targetChanges) {
        return ['found' => true, 'price' => end($prices)];
    }

    // Generate remaining prices and check sequences
    for ($i = 5; $i < 2000; $i++) {
        array_shift($prices);
        $prices[] = generateNextPrice($secretNumber);

        $changes = [];
        for ($j = 0; $j < 4; $j++) {
            $changes[] = $prices[$j + 1] - $prices[$j];
        }
        if ($changes === $targetChanges) {
            return ['found' => true, 'price' => end($prices)];
        }
    }

    return ['found' => false, 'price' => 0];
}

function solvePart2($input): int
{
    $maxBananas = 0;
    $bestSequence = null;
    $bestResults = [];

    // Process first buyer to get all possible sequences
    $firstSecret = (int)$input[0];
    $prices = [];
    $sequences = [];

    // Get initial price but don't use it
    generateNextPrice($firstSecret);

    // Get first 5 prices
    for ($i = 0; $i < 5; $i++) {
        $prices[] = generateNextPrice($firstSecret);
    }

    // Check first sequence
    $changes = [];
    for ($i = 0; $i < 4; $i++) {
        $changes[] = $prices[$i + 1] - $prices[$i];
    }
    $key = implode(',', $changes);
    $sequences[$key] = $changes;

    // Generate remaining prices and collect sequences
    for ($i = 5; $i < 2000; $i++) {
        array_shift($prices);
        $prices[] = generateNextPrice($firstSecret);

        $changes = [];
        for ($j = 0; $j < 4; $j++) {
            $changes[] = $prices[$j + 1] - $prices[$j];
        }
        $key = implode(',', $changes);
        if (!isset($sequences[$key])) {
            $sequences[$key] = $changes;
        }
    }

    // Try each unique sequence
    foreach ($sequences as $changes) {
        $total = 0;
        $currentPrices = [];

        // Check each buyer
        foreach ($input as $buyerIndex => $buyerSecret) {
            $result = findFirstOccurrence($buyerSecret, $changes);
            if ($result['found']) {
                $total += $result['price'];
                $currentPrices[$buyerIndex] = $result['price'];
            }
        }

        if ($total > $maxBananas) {
            $maxBananas = $total;
            $bestSequence = $changes;
            $bestResults = $currentPrices;
        }
    }

    if ($bestSequence) {
        printBestSequenceResults($input, $bestSequence, $bestResults);
    }

    return $maxBananas;
}

// Part 1

$profiler = new Profiler();
$profiler->startProfile();
$result1 = solvePart1($input);
$profiler->stopProfile();
echo "The sum of the 2000th secret number generated by each buyer: {$result1}" . PHP_EOL;
$profiler->reportProfile();

// Part 2

$profiler = new Profiler();
$profiler->startProfile();
$result2 = solvePart2($input);
$profiler->stopProfile();
echo "The most bananas you could get: {$result2}" . PHP_EOL;
$profiler->reportProfile();