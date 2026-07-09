<?php

declare(strict_types=1);

namespace Tavp\Core\Health;

/**
 * Redis health check.
 */
class RedisCheck
{
    public function __construct(private ?object $redis = null)
    {
    }

    public function __invoke(): bool
    {
        if ($this->redis === null) {
            return false;
        }

        try {
            return $this->redis->ping() === '+PONG';
        } catch (\Throwable $e) {
            return false;
        }
    }
}
