<?php

declare(strict_types=1);

namespace Tavp\Core\Console;

/**
 * The root "tavp" command dispatcher.
 *
 * Parses the first argument as a sub-command and delegates to the
 * matching handler. New commands are registered in the $commands map.
 */
class TavpCommand
{
    private array $commands = [];

    public function __construct()
    {
        $this->commands = [
            'new' => NewCommand::class,
            'serve' => ServeCommand::class,
            'migrate' => MigrateCommand::class,
            'make:migration' => MakeMigrationCommand::class,
            'make:controller' => MakeControllerCommand::class,
            'make:model' => MakeModelCommand::class,
            'key:generate' => KeyGenerateCommand::class,
            'deploy' => DeployCommand::class,
            'env:list' => EnvListCommand::class,
        ];
    }

    public function run(array $argv): void
    {
        $name = $argv[1] ?? 'help';

        if ($name === 'help' || $name === '--help' || $name === '-h') {
            $this->printHelp();

            return;
        }

        if (!isset($this->commands[$name])) {
            echo "Unknown command: {$name}\n";
            $this->printHelp();

            return;
        }

        $handler = new ($this->commands[$name])();
        $handler->handle(array_slice($argv, 2));
    }

    private function printHelp(): void
    {
        echo "TAVP — Tailwind + Alpine + Volt + Phalcon\n\n";
        echo "Available commands:\n";
        foreach (array_keys($this->commands) as $command) {
            echo "  tavp {$command}\n";
        }
        echo "  tavp help\n";
    }
}
