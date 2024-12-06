<?php

$lines = file('input06.txt', FILE_IGNORE_NEW_LINES);

// Part 1

// Part 2

function simulateGuardMovement($lines): array
{
    $directions = [[0, -1], [1, 0], [0, 1], [-1, 0]]; // up, right, down, left
    $start = null;

    // Find the starting position
    foreach ($lines as $r => $row) {
        $c = strpos($row, '^');
        if ($c !== false) {
            $start = [$r, $c];
            break;
        }
    }

    function run($G, $start, $P2 = false): array|int
    {
        [$r, $c] = $start;
        $D = [[-1, 0], [0, 1], [1, 0], [0, -1]];
        $dir = 0;

        $V = [];
        $O = 0;

        while (true) {
            if ($P2) {
                $key = "{$r},{$c},{$dir}";
                if (isset($V[$key])) {
                    return 1;
                }
            } else {
                $key = "{$r},{$c}";
                if (!isset($V[$key]) && [$r, $c] !== $start) {
                    $_G = $G;
                    $_G[$r][$c] = "#";
                    $O += run($_G, $start, true);
                }
            }
            $V[$key] = 1;

            [$_r, $_c] = [$r + $D[$dir][0], $c + $D[$dir][1]];

            if ($_r < 0 || $_r >= count($G) || $_c < 0 || $_c >= strlen($G[0])) {
                return $P2 ? 0 : [count($V), $O];
            }

            if ($G[$_r][$_c] == "#") {
                $dir = ++$dir % 4;
            } else {
                [$r, $c] = [$_r, $_c];
            }
        }
    }

    return run($lines, $start);
}

$result = simulateGuardMovement($lines);

echo "Distinct map positions occupied (Part 1): " . $result[0] . PHP_EOL;
echo "Distinct obstruction positions to force loop (Part 2): " . $result[1] . PHP_EOL;
