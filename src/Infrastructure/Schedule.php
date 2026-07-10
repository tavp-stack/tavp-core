<?php

declare(strict_types=1);

namespace Tavp\Core\Infrastructure;

/**
 * Scheduled task runner. Tasks are registered with a cron-like frequency
 * and executed by "tavp schedule:run".
 */
class Schedule
{
    private array $tasks = [];

    /**
     * Register a task with a cron-like frequency string.
     *
     * Supported formats:
     *   '* * * * *'       - every minute
     *   '5 * * * *'       - every hour at minute 5
     *   '0 0/2 * * *'     - every 2 hours
     *   '0 0 * * *'       - daily at midnight
     *   '0 0 * * 0'       - weekly on Sunday
     *   'daily'           - alias for '0 0 * * *'
     *   'hourly'          - alias for '0 * * * *'
     *   'every_minute'    - alias for '* * * * *'
     */
    public function call(callable $task, string $frequency = 'daily'): self
    {
        $this->tasks[] = ['task' => $task, 'frequency' => $frequency];

        return $this;
    }

    /**
     * Run all tasks whose frequency matches the current time.
     */
    public function run(): int
    {
        $count = 0;
        $now = time();

        foreach ($this->tasks as $entry) {
            if ($this->shouldRun($entry['frequency'], $now)) {
                ($entry['task'])();
                $count++;
            }
        }

        return $count;
    }

    /**
     * Check if a task should run based on its frequency and the current time.
     */
    private function shouldRun(string $frequency, int $now): bool
    {
        $frequency = $this->normalizeFrequency($frequency);

        $parts = explode(' ', $frequency);
        if (count($parts) !== 5) {
            return false;
        }

        [$minute, $hour, $dayOfMonth, $month, $dayOfWeek] = $parts;

        $currentMinute = (int) date('i', $now);
        $currentHour = (int) date('G', $now);
        $currentDayOfMonth = (int) date('j', $now);
        $currentMonth = (int) date('n', $now);
        $currentDayOfWeek = (int) date('w', $now);

        return $this->matches($minute, $currentMinute, 0, 59)
            && $this->matches($hour, $currentHour, 0, 23)
            && $this->matches($dayOfMonth, $currentDayOfMonth, 1, 31)
            && $this->matches($month, $currentMonth, 1, 12)
            && $this->matches($dayOfWeek, $currentDayOfWeek, 0, 6);
    }

    /**
     * Check if a cron field matches the current value.
     */
    private function matches(string $field, int $value, int $min, int $max): bool
    {
        if ($field === '*') {
            return true;
        }

        if (str_contains($field, '/')) {
            $parts = explode('/', $field);
            $step = (int) ($parts[1] ?? 1);

            if ($step <= 0) {
                return false;
            }

            return ($value - $min) % $step === 0;
        }

        if (str_contains($field, ',')) {
            $values = array_map('intval', explode(',', $field));

            return in_array($value, $values, true);
        }

        if (str_contains($field, '-')) {
            [$start, $end] = explode('-', $field);

            return $value >= (int) $start && $value <= (int) $end;
        }

        return (int) $field === $value;
    }

    /**
     * Normalize frequency aliases to cron expressions.
     */
    private function normalizeFrequency(string $frequency): string
    {
        return match ($frequency) {
            'daily' => '0 0 * * *',
            'hourly' => '0 * * * *',
            'every_minute' => '* * * * *',
            'weekly' => '0 0 * * 0',
            'monthly' => '0 0 1 * *',
            default => $frequency,
        };
    }
}
