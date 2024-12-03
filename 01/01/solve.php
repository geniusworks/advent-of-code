<?php

/*
 * Maybe the lists are only off by a small amount! To find out, pair up the numbers and measure how far apart they are. Pair up the smallest number in the left list with the smallest number in the right list, then the second-smallest left number with the second-smallest right number, and so on.
 * Within each pair, figure out how far apart the two numbers are; you'll need to add up all of those distances. For example, if you pair up a 3 from the left list with a 7 from the right list, the distance apart is 4; if you pair up a 9 with a 3, the distance apart is 6.
 * To find the total distance between the left list and the right list, add up the distances between all of the pairs you found.
*/

$lines = file('input.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

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
