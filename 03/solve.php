<?php

// Part 1

/*
 * Review and complete code to solve for the following:
 * The goal of the program is just to multiply some numbers. It does that with instructions like mul(X,Y), where X and Y are each 1-3 digit numbers. For instance, mul(44,46) multiplies 44 by 46 to get a result of 2024. Similarly, mul(123,4) would multiply 123 by 4.
 * However, because the program's memory has been corrupted, there are also many invalid characters that should be ignored, even if they look like part of a mul instruction. Sequences like mul(4*, mul(6,9!, ?(12,34), or mul ( 2 , 4 ) do nothing.
 * Scan the corrupted contents of input.txt for uncorrupted mul instructions. What do you get if you add up all of the results of the multiplications?
 */

$lines = file('input.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

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

/*
 * Consider that the previous code is 100% correct and produces the expected output.
 * But now for Part 2, let's consider that there are two new instructions you'll need to handle:
 * 1. The do() instruction enables future mul instructions.
 * 2. The don't() instruction disables future mul instructions.
 * Only the most recent do() or don't() instruction applies. At the beginning of the program, mul instructions are enabled.
 */

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
