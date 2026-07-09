<?php

declare(strict_types=1);

namespace Tavp\Core\Console;

/**
 * tavp phalcon:install — install the Phalcon 5 C-extension.
 *
 * Dispatches the bundled scripts/install-phalcon.sh. This single command
 * removes Phalcon's biggest adoption barrier (compiling the extension),
 * so anyone can enable Phalcon with one step on Lando, VPS or Docker.
 */
class PhalconInstallCommand
{
    public function handle(array $args): void
    {
        $phpVersion = $args[0] ?? '';
        $phalconVersion = $args[1] ?? '5.16.0';

        $script = dirname(__DIR__, 2) . '/scripts/install-phalcon.sh';

        if (!is_file($script)) {
            echo "ERROR: install-phalcon.sh not found at {$script}\n";

            return;
        }

        $arg = ($phpVersion !== '' ? escapeshellarg($phpVersion) : '')
            . ' ' . escapeshellarg($phalconVersion);

        echo "Running Phalcon installer...\n";
        passthru('sh ' . escapeshellarg($script) . ' ' . $arg);
    }
}
