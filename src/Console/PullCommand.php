<?php

declare(strict_types=1);

namespace Tavp\Core\Console;

/**
 * tavp pull — pull changes, install dependencies, run migrations.
 *
 * Usage: tavp pull
 */
class PullCommand
{
    public function handle(array $args): void
    {
        echo "Pulling changes...\n";
        $this->run('git pull');

        echo "Installing dependencies...\n";
        $this->run('composer install --no-interaction');

        echo "Running migrations...\n";
        $migrate = new MigrateCommand();
        $migrate->handle([]);

        echo "Pull complete.\n";
    }

    private function run(string $command): string
    {
        $output = [];
        $exitCode = 0;

        exec($command . ' 2>&1', $output, $exitCode);

        return implode("\n", $output);
    }
}
