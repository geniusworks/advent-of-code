<?php

$lines = file('input.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

// Part 1

$result = 0;

foreach ($lines as $line) {
    preg_match_all('/mul\((\d+),(\d+)\)/', $line, $matches, PREG_SET_ORDER);
    foreach ($matches as $match) {
        echo "Found valid mul instruction: mul(" . $match[1] . "," . $match[2] . ")\n";
        $num1 = (int) $match[1];
        $num2 = (int) $match[2];
        $result += $num1 * $num2;
    }
}

echo "Final result: $result\n";

// Part 2

$part2Result = 0;
$isEnabled = true;

foreach ($lines as $line) {
    preg_match_all('/(do\(\))|(don\'t\(\))|mul\((\d+),(\d+)\)/', $line, $matches, PREG_SET_ORDER);
    foreach ($matches as $match) {
        if ($match[1] == 'do()') {
            $isEnabled = true;
        } elseif ($match[2] == "don't()") {
            $isEnabled = false;
        } elseif ($match[3] && $isEnabled) {
            echo "Found valid mul instruction: mul(" . $match[3] . "," . $match[4] . ")\n";
            $num1 = (int) $match[3];
            $num2 = (int) $match[4];
            $part2Result += $num1 * $num2;
        }
    }
}

echo "Final result (Part 2): $part2Result\n";
