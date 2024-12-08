<?php
/**
 * Advent of Code 2024
 * Day 6: Guard Gallivant
 *
 * @author Martin Diekhoff
 * @link https://adventofcode.com/2024/day/6
 */

require_once '../bootstrap.php';

$input = DataImporter::importFromFileWithDefaultFlags('input06.txt');

class GuardMovementSimulator
{
    private const array DIRECTIONS = [
        [-1, 0],  // up
        [0, 1],   // right
        [1, 0],   // down
        [0, -1],   // left
    ];

    private array $grid;
    private array $startPosition;

    /**
     * @throws Exception
     */
    public function __construct(array $grid)
    {
        $this->grid = $grid;
        $this->startPosition = $this->findStartPosition();
    }

    /**
     * @throws Exception
     */
    private function findStartPosition(): array
    {
        foreach ($this->grid as $r => $row) {
            $c = strpos($row, '^');
            if ($c !== false) {
                return [$r, $c];
            }
        }
        throw new Exception("Start position not found");
    }

    public function simulate(): array
    {
        return $this->run($this->grid, $this->startPosition);
    }

    private function run(array $grid, array $start, bool $detectLoop = false): array|int
    {
        [$r, $c] = $start;
        $dir = 0;
        $visited = [];
        $obstructionCount = 0;

        while (true) {
            $key = $detectLoop
                ? "{$r},{$c},{$dir}"
                : "{$r},{$c}";

            // Part 2 loop detection
            if ($detectLoop) {
                if (isset($visited[$key])) {
                    return 1;
                }
            } // Part 1 obstruction counting
            else {
                if (!isset($visited[$key]) && [$r, $c] !== $start) {
                    $modifiedGrid = $grid;
                    $modifiedGrid[$r][$c] = "#";
                    $obstructionCount += $this->run($modifiedGrid, $start, true);
                }
            }

            $visited[$key] = 1;

            // Calculate next position
            $nextR = $r + self::DIRECTIONS[$dir][0];
            $nextC = $c + self::DIRECTIONS[$dir][1];

            // Boundary check
            if ($nextR < 0 || $nextR >= count($grid) ||
                $nextC < 0 || $nextC >= strlen($grid[0])) {
                return $detectLoop ? 0 : [count($visited), $obstructionCount];
            }

            // Change direction if obstructed
            if ($grid[$nextR][$nextC] == "#") {
                $dir = ($dir + 1) % 4;
            } else {
                [$r, $c] = [$nextR, $nextC];
            }
        }
    }

    public function simulatePart1(): int
    {
        return $this->run($this->grid, $this->startPosition)[0];
    }

    public function simulatePart2(): int
    {
        return $this->run($this->grid, $this->startPosition)[1];
    }
}

try {
    $simulator = new GuardMovementSimulator($input);

    // Part 1

    $profiler = new Profiler('Part 1');
    $profiler->startProfile();
    $part1Result = $simulator->simulatePart1();
    $profiler->stopProfile();
    echo "Distinct map positions occupied: $part1Result" . PHP_EOL;
    $profiler->reportProfile();

    // Part 2

    $profiler = new Profiler('Part 2');
    $profiler->startProfile();
    $part2Result = $simulator->simulatePart2();
    $profiler->stopProfile();
    echo "Distinct obstruction positions to force loop: $part2Result" . PHP_EOL;
    $profiler->reportProfile();
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . PHP_EOL;
}