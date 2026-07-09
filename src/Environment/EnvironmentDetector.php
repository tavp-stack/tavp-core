<?php

declare(strict_types=1);

namespace Tavp\Core\Environment;

/**
 * Detects which environment the application is running in:
 * local, staging, or production.
 *
 * The APP_ENV variable is the source of truth. If it is missing we
 * fall back to a safe default of "local".
 */
class EnvironmentDetector
{
    public function detect(mixed $appEnv): string
    {
        $env = is_string($appEnv) ? strtolower(trim($appEnv)) : '';

        return match ($env) {
            'production', 'prod' => 'production',
            'staging', 'stage' => 'staging',
            default => 'local',
        };
    }

    public function isProduction(string $environment): bool
    {
        return $environment === 'production';
    }

    public function isLocal(string $environment): bool
    {
        return $environment === 'local';
    }
}
