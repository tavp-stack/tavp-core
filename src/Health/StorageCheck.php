<?php

declare(strict_types=1);

namespace Tavp\Core\Health;

/**
 * Storage health check.
 */
class StorageCheck
{
    public function __construct(private string $disk = 'local')
    {
    }

    public function __invoke(): bool
    {
        try {
            $testFile = 'health-check-' . uniqid();
            $storage = app('storage');

            $storage->put($testFile, 'ping');
            $result = $storage->get($testFile);
            $storage->delete($testFile);

            return $result === 'ping';
        } catch (\Throwable $e) {
            return false;
        }
    }
}
