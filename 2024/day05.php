<?php
/**
 * Advent of Code 2024
 * Day 5: Print Queue
 *
 * @author Martin Diekhoff
 * @link https://adventofcode.com/2024/day/5
 */

require_once '../bootstrap.php';

$input = DataImporter::importFromFileWithDefaultFlags('input05.txt');

class UpdateValidator
{
    private array $rules;
    private array $updates;

    public function __construct(array $input)
    {
        [$this->rules, $this->updates] = $this->parseInput($input);
    }

    private function parseInput(array $input): array
    {
        $rules = [];
        $updates = [];
        $ruleSection = true;

        foreach ($input as $line) {
            if (trim($line) === '') {
                $ruleSection = false;
                continue;
            }

            $ruleSection
                ? $rules[] = explode('|', $line)
                : $updates[] = explode(',', $line);
        }

        return [$rules, $updates];
    }

    public function validateUpdates(): array
    {
        return array_filter($this->updates, function ($update) {
            return $this->isUpdateValid($update);
        });
    }

    private function isUpdateValid(array $update): bool
    {
        foreach ($this->rules as $rule) {
            $index0 = array_search($rule[0], $update);
            $index1 = array_search($rule[1], $update);

            if ($index0 !== false && $index1 !== false && $index0 > $index1) {
                return false;
            }
        }
        return true;
    }

    public function getMiddleNumbers(array $updateSet): int
    {
        $middles = array_map(function ($update) {
            $middleIndex = floor(count($update) / 2);
            return (int)$update[$middleIndex];
        }, $updateSet);

        return array_sum($middles);
    }

    public function sortInvalidUpdates(array $updates): array
    {
        return array_map(function ($update) {
            $sortedUpdate = $update;
            for ($i = 0; $i < count($sortedUpdate); $i++) {
                for ($j = $i + 1; $j < count($sortedUpdate); $j++) {
                    foreach ($this->rules as $rule) {
                        if ($sortedUpdate[$i] == $rule[1] && $sortedUpdate[$j] == $rule[0]) {
                            // Swap elements
                            [$sortedUpdate[$i], $sortedUpdate[$j]] = [$sortedUpdate[$j], $sortedUpdate[$i]];
                        }
                    }
                }
            }
            return $sortedUpdate;
        }, $updates);
    }

    public function getInvalidUpdates(): array
    {
        return array_filter($this->updates, function ($update) {
            return !$this->isUpdateValid($update);
        });
    }
}

// Create validator
$validator = new UpdateValidator($input);

// Part 1

$profiler = new Profiler('Part 1');
$profiler->startProfile();

// Part 1: Correctly ordered updates
$validUpdates = $validator->validateUpdates();
$sumOfValidMiddles = $validator->getMiddleNumbers($validUpdates);

$profiler->stopProfile();
echo "Sum of middle numbers for correctly ordered updates: {$sumOfValidMiddles}" . PHP_EOL;
$profiler->reportProfile();

// Part 2

$profiler = new Profiler('Part 2');
$profiler->startProfile();

// Part 2: Reordered invalid updates
$invalidUpdates = $validator->getInvalidUpdates();
$sortedInvalidUpdates = $validator->sortInvalidUpdates($invalidUpdates);
$sumOfInvalidMiddles = $validator->getMiddleNumbers($sortedInvalidUpdates);

$profiler->stopProfile();
echo "Sum of middle numbers for correctly ordered updates: {$sumOfInvalidMiddles}" . PHP_EOL;
$profiler->reportProfile();