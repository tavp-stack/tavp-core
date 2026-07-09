<?php

declare(strict_types=1);

namespace Tavp\Core\Cache;

/**
 * File-based cache store.
 */
class FileStore implements CacheStore
{
    private string $directory;
    private int $ttl;

    public function __construct(array $config)
    {
        $this->directory = $config['path'] ?? storage_path('cache');
        $this->ttl = $config['ttl'] ?? 3600;

        if (!is_dir($this->directory)) {
            mkdir($this->directory, 0755, true);
        }
    }

    public function get(string $key, mixed $default = null): mixed
    {
        $path = $this->getPath($key);

        if (!file_exists($path)) {
            return $default;
        }

        $data = file_get_contents($path);
        $cache = unserialize($data);

        if ($cache['expires'] < time()) {
            unlink($path);
            return $default;
        }

        return $cache['value'];
    }

    public function set(string $key, mixed $value, int $ttl = 3600): bool
    {
        $path = $this->getPath($key);
        $data = [
            'value' => $value,
            'expires' => time() + $ttl,
        ];

        return file_put_contents($path, serialize($data)) !== false;
    }

    public function delete(string $key): bool
    {
        $path = $this->getPath($key);

        if (file_exists($path)) {
            return unlink($path);
        }

        return true;
    }

    public function has(string $key): bool
    {
        return $this->get($key) !== null;
    }

    public function flush(): bool
    {
        $files = glob($this->directory . '/*.cache');
        foreach ($files as $file) {
            unlink($file);
        }
        return true;
    }

    private function getPath(string $key): string
    {
        return $this->directory . '/' . md5($key) . '.cache';
    }
}
