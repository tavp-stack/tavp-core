<?php

declare(strict_types=1);

namespace Tavp\Core\Console;

/**
 * tavp env:switch — switch to a different environment.
 *
 * Usage: tavp env:switch <environment>
 */
class EnvSwitchCommand
{
    public function handle(array $args): void
    {
        $env = $args[0] ?? null;

        if ($env === null) {
            echo "Usage: tavp env:switch <environment>\n";
            echo "Available environments: local, staging, production\n";
            return;
        }

        $validEnvs = ['local', 'staging', 'production'];

        if (!in_array($env, $validEnvs, true)) {
            echo "Invalid environment: {$env}\n";
            echo "Available: " . implode(', ', $validEnvs) . "\n";
            return;
        }

        $envFile = base_path('.env');
        $envFileBackup = base_path('.env.' . $env);

        if (!is_file($envFile)) {
            echo ".env file not found.\n";
            return;
        }

        // Read current .env
        $content = file_get_contents($envFile);

        // Update APP_ENV
        if (preg_match('/^APP_ENV=.*/m', $content)) {
            $content = preg_replace('/^APP_ENV=.*/m', "APP_ENV={$env}", $content);
        } else {
            $content .= "\nAPP_ENV={$env}\n";
        }

        file_put_contents($envFile, $content);

        echo "Switched to [{$env}] environment.\n";
    }
}
