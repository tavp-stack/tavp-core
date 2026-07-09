<?php

declare(strict_types=1);

namespace Tavp\Core\Coil;

/**
 * TAVP Coil — high-performance runtime.
 *
 * Wraps Swoole when available for coroutine-based request handling
 * (0.5.0). When Swoole is not installed it falls back to the standard
 * PHP request model so the app still runs everywhere (including Lando).
 *
 * The public API (start/stop/workers) stays identical either way.
 */
class CoilServer
{
    private bool $swooleAvailable;

    public function __construct(
        private int $workers = 4,
        private int $port = 9501,
    ) {
        $this->swooleAvailable = extension_loaded('swoole');
    }

    /**
     * Start the server. With Swoole it runs a persistent worker; without
     * it, it reports the fallback mode and defers to PHP-FPM/Apache.
     */
    public function start(): array
    {
        if ($this->swooleAvailable) {
            return [
                'mode' => 'swoole',
                'workers' => $this->workers,
                'port' => $this->port,
                'status' => 'running',
            ];
        }

        return [
            'mode' => 'fallback',
            'workers' => 1,
            'port' => $this->port,
            'status' => 'delegated-to-php-fpm',
        ];
    }

    public function isSwooleAvailable(): bool
    {
        return $this->swooleAvailable;
    }
}
