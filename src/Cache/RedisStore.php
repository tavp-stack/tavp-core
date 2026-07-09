<?php

declare(strict_types=1);

namespace Tavp\Core\Cache;

/**
 * Redis cache store.
 */
class RedisStore implements CacheStore
{
    private ?object $redis = null;
    private string $prefix;

    public function __construct(array $config)
    {
        $this->prefix = $config['prefix'] ?? 'tavp:';
    }

    private function connect(): void
    {
        if ($this->redis === null) {
            if (class_exists('Redis')) {
                $this->redis = new \Redis();
                $this->redis->connect('127.0.0.1', 6379);
            } else {
                throw new \RuntimeException('Redis extension not available');
            }
        }
    }

    public function get(string $key, mixed $default = null): mixed
    {
        $this->connect();
        $value = $this->redis->get($this->prefix . $key);
        return $value !== false ? unserialize($value) : $default;
    }

    public function set(string $key, mixed $value, int $ttl = 3600): bool
    {
        $this->connect();
        return $this->redis->setex($this->prefix . $key, $ttl, serialize($value));
    }

    public function delete(string $key): bool
    {
        $this->connect();
        return $this->redis->del($this->prefix . $key) > 0;
    }

    public function has(string $key): bool
    {
        $this->connect();
        return $this->redis->exists($this->prefix . $key);
    }

    public function flush(): bool
    {
        $this->connect();
        $keys = $this->redis->keys($this->prefix . '*');
        foreach ($keys as $key) {
            $this->redis->del($key);
        }
        return true;
    }
}
