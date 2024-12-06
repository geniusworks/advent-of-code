<?php

$lines = file('input05.txt', FILE_IGNORE_NEW_LINES);

// Part 1

$rules = [];
$updates = [];

$ruleSection = true;
foreach ($lines as $line) {
    if (trim($line) === '') {
        $ruleSection = false;
        continue;
    }

    if ($ruleSection) {
        $rules[] = explode('|', $line);
    } else {
        $updates[] = explode(',', $line);
    }
}

$validUpdates = [];
foreach ($updates as $update) {
    $isValid = true;
    foreach ($rules as $rule) {
        if (in_array($rule[0], $update) && in_array($rule[1], $update)) {
            $index0 = array_search($rule[0], $update);
            $index1 = array_search($rule[1], $update);
            if ($index0 > $index1) {
                $isValid = false;
                break;
            }
        }
    }
    if ($isValid) {
        $validUpdates[] = $update;
    }
}

$middles = [];
foreach ($validUpdates as $update) {
    $middleIndex = floor(count($update) / 2);
    $middles[] = (int)$update[$middleIndex];
}

$sum = array_sum($middles);

echo "Sum of middle numbers for correctly ordered updates: $sum\n";

// Part 2

$invalidUpdates = [];
foreach ($updates as $update) {
    $isValid = true;
    foreach ($rules as $rule) {
        if (in_array($rule[0], $update) && in_array($rule[1], $update)) {
            $index0 = array_search($rule[0], $update);
            $index1 = array_search($rule[1], $update);
            if ($index0 > $index1) {
                $isValid = false;
                break;
            }
        }
    }
    if (!$isValid) {
        $invalidUpdates[] = $update;
    }
}

$middlePageNumbers = [];
foreach ($invalidUpdates as $update) {
    $sortedUpdate = $update;
    for ($i = 0; $i < count($sortedUpdate); $i++) {
        for ($j = $i + 1; $j < count($sortedUpdate); $j++) {
            foreach ($rules as $rule) {
                if ($sortedUpdate[$i] == $rule[1] && $sortedUpdate[$j] == $rule[0]) {
                    $temp = $sortedUpdate[$i];
                    $sortedUpdate[$i] = $sortedUpdate[$j];
                    $sortedUpdate[$j] = $temp;
                }
            }
        }
    }
    $middleIndex = floor(count($sortedUpdate) / 2);
    $middlePageNumbers[] = (int)$sortedUpdate[$middleIndex];
}

$sumOfMiddlePageNumbers = array_sum($middlePageNumbers);

echo "Sum of middle numbers for incorrectly ordered updates: $sumOfMiddlePageNumbers\n";
