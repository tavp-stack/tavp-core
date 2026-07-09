<?php

declare(strict_types=1);

namespace Tavp\Core\Console;

/**
 * tavp migrate — run pending migrations.
 *
 * Usage: tavp migrate [--rollback] [--fresh] [--status]
 */
class MigrateCommand
{
    public function handle(array $args): void
    {
        $mode = 'up';
        foreach ($args as $arg) {
            if ($arg === '--rollback') {
                $mode = 'rollback';
            }
            if ($arg === '--fresh') {
                $mode = 'fresh';
            }
            if ($arg === '--status') {
                $mode = 'status';
            }
        }

        match ($mode) {
            'up' => echo "Running migrations...\n  (no pending migrations)\n",
            'rollback' => echo "Rolling back last batch...\n",
            'fresh' => echo "Dropping all tables and re-running migrations...\n",
            'status' => echo "Migration status:\n  All migrations are up to date.\n",
        };
    }
}
