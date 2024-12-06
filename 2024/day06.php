<?php

// Advent of Code 2024 Day 6
// Martin Diekhoff

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
}

// Main script
$lines = file('input06.txt', FILE_IGNORE_NEW_LINES);

try {
    $simulator = new GuardMovementSimulator($lines);
    $result = $simulator->simulate();

    echo "Distinct map positions occupied (Part 1): " . $result[0] . PHP_EOL;
    echo "Distinct obstruction positions to force loop (Part 2): " . $result[1] . PHP_EOL;
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . PHP_EOL;
}
