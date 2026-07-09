<?php

declare(strict_types=1);

namespace Tavp\Core\Infrastructure;

/**
 * Cache abstraction with file and array drivers.
 *
 * Redis/APCu/Memcached drivers are added in production builds; the
 * interface stays identical so application code is driver-agnostic.
 */
class Cache
{
    public function __construct(private string $driver = 'file', private string $path = '')
    {
    }

    public function get(string $key, mixed $default = null): mixed
    {
        if ($this->driver === 'array') {
            return $this->array[$key] ?? $default;
        }

        $file = $this->file($key);
        if (!is_file($file)) {
            return $default;
        }

        $data = json_decode(file_get_contents($file), true);
        if ($data['expires_at'] !== null && $data['expires_at'] < time()) {
            unlink($file);

            return $default;
        }

        return $data['value'];
    }

    public function set(string $key, mixed $value, int $ttlSeconds = 3600): void
    {
        if ($this->driver === 'array') {
            $this->array[$key] = $value;

            return;
        }

        $file = $this->file($key);
        file_put_contents($file, json_encode([
            'value' => $value,
            'expires_at' => $ttlSeconds > 0 ? time() + $ttlSeconds : null,
        ]));
    }

    public function forget(string $key): void
    {
        if (is_file($this->file($key))) {
            unlink($this->file($key));
        }
        unset($this->array[$key]);
    }

    private function file(string $key): string
    {
        return $this->path . '/' . sha1($key) . '.cache';
    }

    private array $array = [];
}
