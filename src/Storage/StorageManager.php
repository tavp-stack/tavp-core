<?php

declare(strict_types=1);

namespace Tavp\Core\Storage;

/**
 * Storage manager — driver-based file storage abstraction.
 */
class StorageManager
{
    private array $disks = [];
    private string $defaultDisk;

    public function __construct(array $config)
    {
        $this->defaultDisk = $config['default'] ?? 'local';

        foreach ($config['disks'] ?? [] as $name => $diskConfig) {
            $this->disks[$name] = match ($diskConfig['driver'] ?? 'local') {
                'local' => new LocalDisk($diskConfig),
                's3' => new S3Disk($diskConfig),
                'minio' => new S3Disk($diskConfig), // MinIO uses S3-compatible API
                default => new LocalDisk($diskConfig),
            };
        }
    }

    /**
     * Get the contents of a file.
     */
    public function get(string $path): ?string
    {
        return $this->disk()->get($path);
    }

    /**
     * Store a file.
     */
    public function put(string $path, string $content): bool
    {
        return $this->disk()->put($path, $content);
    }

    /**
     * Delete a file.
     */
    public function delete(string $path): bool
    {
        return $this->disk()->delete($path);
    }

    /**
     * Check if a file exists.
     */
    public function exists(string $path): bool
    {
        return $this->disk()->exists($path);
    }

    /**
     * Get the URL for a file.
     */
    public function url(string $path): string
    {
        return $this->disk()->url($path);
    }

    /**
     * Copy a file.
     */
    public function copy(string $from, string $to): bool
    {
        return $this->disk()->copy($from, $to);
    }

    /**
     * Move a file.
     */
    public function move(string $from, string $to): bool
    {
        return $this->disk()->move($from, $to);
    }

    /**
     * Get file size.
     */
    public function size(string $path): int
    {
        return $this->disk()->size($path);
    }

    /**
     * Get the last modified time.
     */
    public function lastModified(string $path): int
    {
        return $this->disk()->lastModified($path);
    }

    /**
     * List files in a directory.
     */
    public function files(string $directory = ''): array
    {
        return $this->disk()->files($directory);
    }

    private function disk(?string $name = null): StorageDisk
    {
        $disk = $name ?? $this->defaultDisk;
        return $this->disks[$disk] ?? $this->disks[$this->defaultDisk];
    }
}

interface StorageDisk
{
    public function get(string $path): ?string;
    public function put(string $path, string $content): bool;
    public function delete(string $path): bool;
    public function exists(string $path): bool;
    public function url(string $path): string;
    public function copy(string $from, string $to): bool;
    public function move(string $from, string $to): bool;
    public function size(string $path): int;
    public function lastModified(string $path): int;
    public function files(string $directory = ''): array;
}
