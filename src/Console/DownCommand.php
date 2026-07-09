<?php

declare(strict_types=1);

namespace Tavp\Core\Console;

/**
 * tavp down — put the application in maintenance mode.
 *
 * Usage: tavp down [--secret=code] [--retry=60]
 */
class DownCommand
{
    public function handle(array $args): void
    {
        $secret = $this->extractOption($args, '--secret', null);
        $retry = (int) $this->extractOption($args, '--retry', '60');

        $maintenanceFile = storage_path('maintenance.json');

        $data = [
            'down' => true,
            'since' => date('c'),
            'secret' => $secret,
            'retry' => $retry,
        ];

        file_put_contents($maintenanceFile, json_encode($data, JSON_PRETTY_PRINT));

        echo "Application is now in maintenance mode.\n";

        if ($secret !== null) {
            echo "Secret bypass: /secret/{$secret}\n";
        }
    }

    private function extractOption(array $args, string $key, ?string $default): ?string
    {
        foreach ($args as $arg) {
            if (str_starts_with($arg, $key . '=')) {
                return substr($arg, strlen($key) + 1);
            }
        }

        return $default;
    }
}
