#!/usr/bin/env php

<?php

$year = date('Y'); // current year
$day = $argv[1]; // day number provided as a command-line argument

if (!is_numeric($day) || $day < 1 || $day > 25) {
    echo "Invalid day number. Please provide a number between 1 and 25." . PHP_EOL;
    exit(1);
}

$paddedDay = str_pad($day, 2, '0', STR_PAD_LEFT);
$fileName = "day{$paddedDay}.php";
$inputFileName = "input{$paddedDay}.txt";
$yearDir = "{$year}/";

if (file_exists($yearDir . $fileName) || file_exists($yearDir . $inputFileName)) {
    echo "File(s) already exist. Exiting safely." . PHP_EOL;
    exit(0);
}

$template = <<<EOT
<?php
/**
 * Advent of Code {$year}
 * Day {$day}:
 *
 * @author Martin Diekhoff
 * @link https://adventofcode.com/{$year}/day/{$day}
 */

const DATA_INPUT_FILE = 'input{$paddedDay}.txt';

require_once __DIR__ . '/../' . 'bootstrap.php';

\$input = DataImporter::importFromFileWithDefaultFlags(__DIR__ . '/' . DATA_INPUT_FILE);

// ...

// Part 1

\$profiler = new Profiler('Part 1');
\$profiler->startProfile();
\$result1 = null; // TODO: Calculate the result for part 1.
\$profiler->stopProfile();
echo "Result = {\$result1}" . PHP_EOL;
\$profiler->reportProfile();

// Part 2

\$profiler = new Profiler('Part 2');
\$profiler->startProfile();
\$result2 = null; // TODO: Calculate the result for part 2.
\$profiler->stopProfile();
echo "Result = {\$result2}" . PHP_EOL;
\$profiler->reportProfile();
EOT;

file_put_contents($yearDir . $fileName, $template);
touch($yearDir . $inputFileName); // create an empty input file

echo "Files {$fileName} and {$inputFileName} generated successfully in {$year} directory." . PHP_EOL;