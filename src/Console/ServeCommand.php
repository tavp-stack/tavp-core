<?php

declare(strict_types=1);

namespace Tavp\Core\Console;

/**
 * tavp serve — start a development server.
 *
 * Detects the active environment adapter (Lando, Docker, native) and
 * starts the appropriate server. Falls back to PHP's built-in server.
 */
class ServeCommand
{
    public function handle(array $args): void
    {
        $port = '8000';
        foreach ($args as $arg) {
            if (str_starts_with($arg, '--port=')) {
                $port = substr($arg, strlen('--port='));
            }
        }

        $adapter = $this->detectAdapter();

        if ($adapter === 'lando') {
            echo "Lando detected. Run: lando start && lando ssh --command=\"php -S 0.0.0.0:{$port}\"\n";

            return;
        }

        echo "Starting PHP development server on http://localhost:{$port}\n";
        echo "Press Ctrl+C to stop.\n";
        passthru("php -S localhost:{$port} -t public");
    }

    private function detectAdapter(): string
    {
        if (getenv('LANDO') !== false || is_file('.lando.yml')) {
            return 'lando';
        }
        if (is_file('docker-compose.yml')) {
            return 'docker';
        }

        return 'native';
    }
}
