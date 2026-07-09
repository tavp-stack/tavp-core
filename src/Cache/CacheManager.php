<?php

declare(strict_types=1);

namespace Tavp\Core\Cache;

/**
 * Cache manager — driver-based cache abstraction.
 */
class CacheManager
{
    private array $drivers = [];
    private string $defaultDriver;

    public function __construct(array $config)
    {
        $this->defaultDriver = $config['default'] ?? 'file';

        foreach ($config['stores'] ?? [] as $name => $storeConfig) {
            $this->drivers[$name] = match ($storeConfig['driver'] ?? 'file') {
                'file' => new FileStore($storeConfig),
                'redis' => new RedisStore($storeConfig),
                'apcu' => new ApcuStore($storeConfig),
                default => new FileStore($storeConfig),
            };
        }
    }

    /**
     * Get a cache value.
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return $this->driver()->get($key, $default);
    }

    /**
     * Set a cache value.
     */
    public function set(string $key, mixed $value, int $ttl = 3600): bool
    {
        return $this->driver()->set($key, $value, $ttl);
    }

    /**
     * Delete a cache value.
     */
    public function delete(string $key): bool
    {
        return $this->driver()->delete($key);
    }

    /**
     * Check if a cache key exists.
     */
    public function has(string $key): bool
    {
        return $this->driver()->has($key);
    }

    /**
     * Get or set cache value.
     */
    public function remember(string $key, int $ttl, callable $callback): mixed
    {
        $value = $this->get($key);

        if ($value !== null) {
            return $value;
        }

        $value = $callback();
        $this->set($key, $value, $ttl);
        return $value;
    }

    /**
     * Flush all cache.
     */
    public function flush(): bool
    {
        return $this->driver()->flush();
    }

    /**
     * Get the cache driver.
     */
    private function driver(?string $name = null): CacheStore
    {
        $driver = $name ?? $this->defaultDriver;
        return $this->drivers[$driver] ?? $this->drivers[$this->defaultDriver];
    }
}

interface CacheStore
{
    public function get(string $key, mixed $default = null): mixed;
    public function set(string $key, mixed $value, int $ttl = 3600): bool;
    public function delete(string $key): bool;
    public function has(string $key): bool;
    public function flush(): bool;
}
