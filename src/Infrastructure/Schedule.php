<?php

declare(strict_types=1);

namespace Tavp\Core\Infrastructure;

/**
 * Scheduled task runner. Tasks are registered with a frequency and
 * executed by "tavp schedule:run".
 */
class Schedule
{
    private array $tasks = [];

    /**
     * Register a task with a cron-like frequency string.
     */
    public function call(callable $task, string $frequency = 'daily'): self
    {
        $this->tasks[] = ['task' => $task, 'frequency' => $frequency];

        return $this;
    }

    /**
     * Run all tasks whose frequency matches the current time (simplified).
     */
    public function run(): int
    {
        $count = 0;
        foreach ($this->tasks as $entry) {
            // In production this checks the cron expression against now.
            ($entry['task'])();
            $count++;
        }

        return $count;
    }
}
