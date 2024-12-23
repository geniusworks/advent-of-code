<?php
/**
 * Advent of Code 2024
 * Day 23: LAN Party
 *
 * @author Martin Diekhoff
 * @link https://adventofcode.com/2024/day/23
 */

const DATA_INPUT_FILE = 'input23.txt';

require_once __DIR__ . '/../' . 'bootstrap.php';

$input = DataImporter::importFromFileWithDefaultFlags(__DIR__ . '/' . DATA_INPUT_FILE);

function solvePart1($input): int
{
    $connections = is_array($input) ? $input : explode("\n", $input);
    $computers = [];

    foreach ($connections as $connection) {
        [$comp1, $comp2] = explode('-', $connection);
        $computers[$comp1][] = $comp2;
        $computers[$comp2][] = $comp1;
    }

    $sets = [];
    foreach ($computers as $comp => $connected) {
        foreach ($connected as $c1) {
            foreach ($connected as $c2) {
                if ($c1 !== $c2 && isset($computers[$c1]) && isset($computers[$c2]) &&
                    in_array($c1, $computers[$c2]) && in_array($c2, $computers[$c1])) {
                    $set = [$comp, $c1, $c2];
                    sort($set); // Sort the set to ensure consistent order
                    $sets[implode(',', $set)] = $set;
                }
            }
        }
    }

    $validSets = [];
    foreach ($sets as $set) {
        $valid = true;
        foreach ($set as $comp) {
            if (count(array_intersect($computers[$comp], $set)) != 2) {
                $valid = false;
                break;
            }
        }
        if ($valid && preg_grep('/^t/', $set)) {
            $validSets[] = $set;
        }
    }

    return count($validSets);
}

function solvePart2($input): string
{
    // Parse the input into a graph representation
    $connections = is_array($input) ? $input : explode("\n", $input);
    $graph = [];
    foreach ($connections as $connection) {
        [$comp1, $comp2] = explode('-', $connection);
        $graph[$comp1][$comp2] = true;
        $graph[$comp2][$comp1] = true;
    }

    // Helper to find all cliques using Bron-Kerbosch algorithm
    function findCliques($graph, $potentialClique = [], $remainingNodes = null, $skipNodes = [])
    {
        $cliques = [];
        if ($remainingNodes === null) {
            $remainingNodes = array_keys($graph);
        }

        if (empty($remainingNodes) && empty($skipNodes)) {
            $cliques[] = $potentialClique;
            return $cliques;
        }

        foreach ($remainingNodes as $node) {
            $newClique = array_merge($potentialClique, [$node]);
            $newRemaining = array_intersect(array_keys($graph[$node]), $remainingNodes);
            $newSkip = array_intersect(array_keys($graph[$node]), $skipNodes);
            $cliques = array_merge(
                $cliques,
                findCliques($graph, $newClique, $newRemaining, $newSkip),
            );
            $remainingNodes = array_diff($remainingNodes, [$node]);
            $skipNodes[] = $node;
        }

        return $cliques;
    }

    // Find all cliques in the graph
    $cliques = findCliques($graph);

    // Find the largest clique
    $largestClique = [];
    foreach ($cliques as $clique) {
        if (count($clique) > count($largestClique)) {
            $largestClique = $clique;
        }
    }

    // Sort the largest clique and create the password
    sort($largestClique);
    return implode(',', $largestClique);
}

// Part 1

$profiler = new Profiler();
$profiler->startProfile();
$result1 = solvePart1($input);
$profiler->stopProfile();
echo "Three inter-connected computers at least one starting with letter 't': {$result1}" . PHP_EOL;
$profiler->reportProfile();

// Part 2

$profiler = new Profiler();
$profiler->startProfile();
$result2 = solvePart2($input);
$profiler->stopProfile();
echo "Password to get into the LAN party: {$result2}" . PHP_EOL;
$profiler->reportProfile();