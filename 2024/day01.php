<?php

$lines = file('input01.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

// Part 1

$leftList = [];
$rightList = [];

// Process each line to extract the left and right numbers
foreach ($lines as $line) {
    list($left, $right) = array_map('intval', explode('   ', $line));
    $leftList[] = $left;
    $rightList[] = $right;
}

// Sort both lists
sort($leftList);
sort($rightList);

// Calculate the total distance
$totalDistance = 0;
for ($i = 0; $i < count($leftList); $i++) {
    $totalDistance += abs($leftList[$i] - $rightList[$i]);
}

echo "Total Distance: " . $totalDistance . PHP_EOL;

// Part 2

// Calculate the minimum distance
$minDistance = PHP_INT_MAX;
for ($i = 0; $i < count($leftList); $i++) {
    $distance = abs($leftList[$i] - $rightList[$i]);
    if ($distance < $minDistance) {
        $minDistance = $distance;
    }
}

// Calculate the maximum distance
$maxDistance = 0;
for ($i = 0; $i < count($leftList); $i++) {
    $distance = abs($leftList[$i] - $rightList[$i]);
    if ($distance > $maxDistance) {
        $maxDistance = $distance;
    }
}

// Calculate the similarity score
$similarityScore = 0;
foreach ($leftList as $left) {
    $count = 0;
    foreach ($rightList as $right) {
        if ($left == $right) {
            $count++;
        }
    }
    $similarityScore += $left * $count;
}

// Print the results
echo "Minimum Distance: " . $minDistance . PHP_EOL;
echo "Maximum Distance: " . $maxDistance . PHP_EOL;
echo "Similarity Score: " . $similarityScore . PHP_EOL;
