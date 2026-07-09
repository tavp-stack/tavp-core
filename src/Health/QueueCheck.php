<?php

declare(strict_types=1);

namespace Tavp\Core\Health;

/**
 * Queue health check.
 */
class QueueCheck
{
    public function __construct(private string $queue = 'default')
    {
    }

    public function __invoke(): bool
    {
        try {
            $queueManager = app('queue');
            $size = $queueManager->size($this->queue);
            return $size >= 0;
        } catch (\Throwable $e) {
            return false;
        }
    }
}
