<?php
/**
 * Profiler class for tracking memory usage and execution time.
 *
 * This class provides a more accurate way to track memory usage and execution time
 * for different parts of the application, ensuring each profile instance
 * reports its own unique memory consumption.
 *
 * @package utils
 */

class Profiler
{
    private string $profileName;
    private float $startTime;
    private int $initialMemory;
    private int $peakMemory;

    public function __construct($profileName)
    {
        $this->profileName = $profileName;
        $this->initialMemory = 0;
        $this->peakMemory = 0;
    }

    public function startProfile(): void
    {
        // Use memory_get_usage(false) to get the real memory usage
        $this->initialMemory = memory_get_usage(false);
        $this->startTime = microtime(true);
        $this->peakMemory = $this->initialMemory;
    }

    public function updatePeakMemory(): void
    {
        $currentMemory = memory_get_usage(false);
        if ($currentMemory > $this->peakMemory) {
            $this->peakMemory = $currentMemory;
        }
    }

    public function stopProfile(): ?array
    {
        $endTime = microtime(true);
        $this->updatePeakMemory();

        return [
            'startTime' => $this->startTime,
            'endTime' => $endTime,
            'initialMemory' => $this->initialMemory,
            'peakMemory' => $this->peakMemory,
            'memoryDelta' => $this->peakMemory - $this->initialMemory,
        ];
    }

    public function reportProfile(): void
    {
        $profile = $this->stopProfile();
        echo "Profile Name: {$this->profileName}" . PHP_EOL;
        echo "Execution Time: " . ($profile['endTime'] - $profile['startTime']) . " seconds" . PHP_EOL;
        echo "Initial Memory Usage: " . $profile['initialMemory'] . " bytes" . PHP_EOL;
        echo "Peak Memory Usage: " . $profile['peakMemory'] . " bytes" . PHP_EOL;
        echo "Memory Increase: " . $profile['memoryDelta'] . " bytes" . PHP_EOL;
        echo PHP_EOL;
    }
}