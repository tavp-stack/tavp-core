<?php

declare(strict_types=1);

namespace Tavp\Core\Console;

/**
 * tavp schedule:run — run scheduled tasks that are due.
 *
 * Usage: tavp schedule:run
 *
 * This command is designed to be called by a system cron every minute:
 *   * * * * * cd /path/to/project && tavp schedule:run
 */
class ScheduleRunCommand
{
    public function handle(array $args): void
    {
        $schedule = $this->loadSchedule();

        if ($schedule === null) {
            echo "No schedule defined. Create schedule.php in your config directory.\n";
            return;
        }

        echo "Running scheduled tasks...\n";

        $count = $schedule->run();

        echo "Executed {$count} task(s).\n";
    }

    private function loadSchedule(): ?\Tavp\Core\Infrastructure\Schedule
    {
        $schedulePath = base_path('config/schedule.php');

        if (!is_file($schedulePath)) {
            return null;
        }

        $schedule = require $schedulePath;

        if (!$schedule instanceof \Tavp\Core\Infrastructure\Schedule) {
            echo "config/schedule.php must return a Schedule instance.\n";
            return null;
        }

        return $schedule;
    }
}
