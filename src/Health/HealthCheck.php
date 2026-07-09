<?php

declare(strict_types=1);

namespace Tavp\Core\Health;

/**
 * Application health endpoint data.
 *
 * Reports status of database, cache and queue so load balancers and
 * uptime monitors can verify the app is healthy.
 */
class HealthCheck
{
    /**
     * @param array<string, bool> $checks
     */
    public function __construct(private array $checks = [])
    {
    }

    public function addCheck(string $name, bool $healthy): void
    {
        $this->checks[$name] = $healthy;
    }

    /**
     * Return the health payload and overall status.
     */
    public function report(): array
    {
        $healthy = !in_array(false, $this->checks, true);

        return [
            'status' => $healthy ? 'ok' : 'degraded',
            'checks' => $this->checks,
            'time' => date('c'),
        ];
    }

    public function isHealthy(): bool
    {
        return !in_array(false, $this->checks, true);
    }
}
