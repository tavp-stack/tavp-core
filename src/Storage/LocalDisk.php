<?php

declare(strict_types=1);

namespace Tavp\Core\Storage;

/**
 * Local filesystem storage.
 */
class LocalDisk implements StorageDisk
{
    private string $root;

    public function __construct(array $config)
    {
        $this->root = $config['root'] ?? storage_path('app');
    }

    public function get(string $path): ?string
    {
        $fullPath = $this->root . '/' . $path;
        if (!file_exists($fullPath)) {
            return null;
        }
        return file_get_contents($fullPath);
    }

    public function put(string $path, string $content): bool
    {
        $fullPath = $this->root . '/' . $path;
        $dir = dirname($fullPath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        return file_put_contents($fullPath, $content) !== false;
    }

    public function delete(string $path): bool
    {
        $fullPath = $this->root . '/' . $path;
        if (file_exists($fullPath)) {
            return unlink($fullPath);
        }
        return true;
    }

    public function exists(string $path): bool
    {
        return file_exists($this->root . '/' . $path);
    }

    public function url(string $path): string
    {
        return '/storage/' . $path;
    }

    public function copy(string $from, string $to): bool
    {
        return copy($this->root . '/' . $from, $this->root . '/' . $to);
    }

    public function move(string $from, string $to): bool
    {
        return rename($this->root . '/' . $from, $this->root . '/' . $to);
    }

    public function size(string $path): int
    {
        $fullPath = $this->root . '/' . $path;
        return file_exists($fullPath) ? filesize($fullPath) : 0;
    }

    public function lastModified(string $path): int
    {
        $fullPath = $this->root . '/' . $path;
        return file_exists($fullPath) ? filemtime($fullPath) : 0;
    }

    public function files(string $directory = ''): array
    {
        $fullPath = $this->root . '/' . $directory;
        if (!is_dir($fullPath)) {
            return [];
        }
        return glob($fullPath . '/*');
    }
}
