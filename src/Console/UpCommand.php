<?php

declare(strict_types=1);

namespace Tavp\Core\Console;

/**
 * tavp up — take the application out of maintenance mode.
 *
 * Usage: tavp up
 */
class UpCommand
{
    public function handle(array $args): void
    {
        $maintenanceFile = storage_path('maintenance.json');

        if (!is_file($maintenanceFile)) {
            echo "Application is not in maintenance mode.\n";
            return;
        }

        unlink($maintenanceFile);

        echo "Application is now live.\n";
    }
}
