<?php

declare(strict_types=1);

namespace Tavp\Core\Console;

/**
 * tavp push — add, commit, and push changes.
 *
 * Usage: tavp push [message]
 */
class PushCommand
{
    public function handle(array $args): void
    {
        $message = $args[0] ?? 'Update from tavp push';

        $this->run('git add -A');
        $this->run("git commit -m \"{$message}\"");
        $this->run('git push');

        echo "Pushed to remote.\n";
    }

    private function run(string $command): string
    {
        $output = [];
        $exitCode = 0;

        exec($command . ' 2>&1', $output, $exitCode);

        return implode("\n", $output);
    }
}
