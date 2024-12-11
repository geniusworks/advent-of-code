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
        $ruleMap = [];
        foreach ($this->rules as $rule) {
            $ruleMap[$rule[0]][$rule[1]] = true;
        }

        foreach ($updates as &$update) {
            $updateSize = count($update);
            for ($i = 0; $i < $updateSize - 1; $i++) {
                for ($j = $i + 1; $j < $updateSize; $j++) {
                    if (isset($ruleMap[$update[$i]][$update[$j]])) {
                        // Swap elements
                        [$update[$i], $update[$j]] = [$update[$j], $update[$i]];
                    }
                }
            }
        }

        return $updates;
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
$validUpdates = $validator->validateUpdates();
$sumOfValidMiddles = $validator->getMiddleNumbers($validUpdates);
$profiler->stopProfile();
echo "Sum of middle numbers for correctly-ordered updates: {$sumOfValidMiddles}" . PHP_EOL;
$profiler->reportProfile();

// Part 2

$profiler = new Profiler('Part 2');
$profiler->startProfile();
$invalidUpdates = $validator->getInvalidUpdates();
$sortedInvalidUpdates = $validator->sortInvalidUpdates($invalidUpdates);
$sumOfInvalidMiddles = $validator->getMiddleNumbers($sortedInvalidUpdates);
$profiler->stopProfile();
echo "Sum of middle numbers for incorrectly-ordered updates: {$sumOfInvalidMiddles}" . PHP_EOL;
$profiler->reportProfile();