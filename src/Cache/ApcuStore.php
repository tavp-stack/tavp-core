<?php

declare(strict_types=1);

namespace Tavp\Core\Cache;

/**
 * APCu cache store.
 */
class ApcuStore implements CacheStore
{
    public function get(string $key, mixed $default = null): mixed
    {
        if (!function_exists('apcu_fetch')) {
            return $default;
        }

        $value = apcu_fetch($key, $success);
        return $success ? $value : $default;
    }

    public function set(string $key, mixed $value, int $ttl = 3600): bool
    {
        if (!function_exists('apcu_store')) {
            return false;
        }

        return apcu_store($key, $value, $ttl);
    }

    public function delete(string $key): bool
    {
        if (!function_exists('apcu_delete')) {
            return false;
        }

        return apcu_delete($key);
    }

    public function has(string $key): bool
    {
        if (!function_exists('apcu_exists')) {
            return false;
        }

        return apcu_exists($key);
    }

    public function flush(): bool
    {
        if (!function_exists('apcu_clear_cache')) {
            return false;
        }

        return apcu_clear_cache('user');
    }
}
