<?php

declare(strict_types=1);

namespace Tavp\Core\Http\Middleware;

use Tavp\Core\Http\Request;
use Tavp\Core\Http\Response;

/**
 * Limits how often a single IP may hit a route within a time window.
 * Uses a simple file-based counter in storage/cache so it works without
 * Redis. Replace with a shared cache in production clusters.
 */
class ThrottleRequests implements Middleware
{
    public function __construct(
        private int $maxAttempts = 60,
        private int $decaySeconds = 60,
    ) {
    }

    public function handle(callable $next): mixed
    {
        $request = new Request();
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $key = 'throttle_' . md5($ip . ($_SERVER['REQUEST_URI'] ?? ''));

        $cacheFile = storage_path('cache/' . $key . '.json');
        $now = time();

        $record = is_file($cacheFile)
            ? json_decode(file_get_contents($cacheFile), true)
            : ['count' => 0, 'reset_at' => $now + $this->decaySeconds];

        if ($now > $record['reset_at']) {
            $record = ['count' => 0, 'reset_at' => $now + $this->decaySeconds];
        }

        $record['count']++;

        if ($record['count'] > $this->maxAttempts) {
            return (new Response())
                ->setStatusCode(429)
                ->setContent('Too many requests. Please slow down.');
        }

        file_put_contents($cacheFile, json_encode($record));

        return $next();
    }
}
