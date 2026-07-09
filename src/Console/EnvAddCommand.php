<?php

declare(strict_types=1);

namespace Tavp\Core\Console;

/**
 * tavp env:add — add a new environment configuration.
 *
 * Usage: tavp env:add <environment>
 */
class EnvAddCommand
{
    public function handle(array $args): void
    {
        $env = $args[0] ?? null;

        if ($env === null) {
            echo "Usage: tavp env:add <environment>\n";
            echo "Example: tavp env:add staging\n";
            return;
        }

        $envFile = base_path('.env.' . $env);

        if (is_file($envFile)) {
            echo "Environment file already exists: .env.{$env}\n";
            return;
        }

        $template = <<<ENV
APP_NAME="TAVP App"
APP_ENV={$env}
APP_KEY=
APP_DEBUG=false
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=tavp_{$env}
DB_USERNAME=root
DB_PASSWORD=

JWT_SECRET=
OTP_LENGTH=6
OTP_TTL_MINUTES=5
OTP_MAX_ATTEMPTS=5

CACHE_DRIVER=file
SESSION_DRIVER=file
MAIL_DRIVER=log

ENV;

        file_put_contents($envFile, $template);

        echo "Created environment file: .env.{$env}\n";
        echo "Edit the file and run: tavp env:switch {$env}\n";
    }
}
