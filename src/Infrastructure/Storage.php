<?php

declare(strict_types=1);

namespace Tavp\Core\Infrastructure;

/**
 * Storage abstraction for files (local driver; S3/MinIO added later).
 */
class Storage
{
    public function __construct(private string $disk = 'local', private string $root = '')
    {
    }

    public function put(string $path, string $contents): bool
    {
        $full = $this->root . '/' . ltrim($path, '/');
        if (!is_dir(dirname($full))) {
            mkdir(dirname($full), 0755, true);
        }

        return file_put_contents($full, $contents) !== false;
    }

    public function get(string $path): ?string
    {
        $full = $this->root . '/' . ltrim($path, '/');

        return is_file($full) ? file_get_contents($full) : null;
    }

    public function exists(string $path): bool
    {
        return is_file($this->root . '/' . ltrim($path, '/'));
    }
}
