<?php

$lines = file('input05.txt', FILE_IGNORE_NEW_LINES);

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

echo "Rules:\n";
print_r($rules);
echo "\nUpdates:\n";
print_r($updates);

$validUpdates = [];
foreach ($updates as $update) {
    echo "Checking update: " . implode(',', $update) . "\n";
    $isValid = true;
    foreach ($rules as $rule) {
        if (in_array($rule[0], $update) && in_array($rule[1], $update)) {
            $index0 = array_search($rule[0], $update);
            $index1 = array_search($rule[1], $update);
            echo "  Rule: " . $rule[0] . "|" . $rule[1] . ", Indexes: $index0, $index1\n";
            if ($index0 > $index1) {
                echo "  Invalid update: $index0 > $index1\n";
                $isValid = false;
                break;
            }
        }
    }
    if ($isValid) {
        echo "  Valid update!\n";
        $validUpdates[] = $update;
    }
}

echo "\nValid Updates:\n";
print_r($validUpdates);

$middles = [];
foreach ($validUpdates as $update) {
    $middleIndex = floor(count($update) / 2);
    echo "  Middle index: $middleIndex, Middle value: " . $update[$middleIndex] . "\n";
    $middles[] = (int)$update[$middleIndex];
}

$sum = array_sum($middles);

echo "\nSum of middle numbers: $sum\n";

// Part 2

