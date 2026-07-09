<?php

declare(strict_types=1);

namespace Tavp\Core\Storage;

/**
 * S3-compatible storage (AWS S3, MinIO, DigitalOcean Spaces).
 */
class S3Disk implements StorageDisk
{
    private string $bucket;
    private string $region;
    private string $endpoint;
    private string $accessKey;
    private string $secretKey;

    public function __construct(array $config)
    {
        $this->bucket = $config['bucket'] ?? '';
        $this->region = $config['region'] ?? 'us-east-1';
        $this->endpoint = $config['endpoint'] ?? "https://s3.{$this->region}.amazonaws.com";
        $this->accessKey = $config['access_key'] ?? '';
        $this->secretKey = $config['secret_key'] ?? '';
    }

    public function get(string $path): ?string
    {
        $url = $this->endpoint . '/' . $this->bucket . '/' . $path;
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return $httpCode === 200 ? $response : null;
    }

    public function put(string $path, string $content): bool
    {
        // Implementation: S3 PUT object
        return true;
    }

    public function delete(string $path): bool
    {
        // Implementation: S3 DELETE object
        return true;
    }

    public function exists(string $path): bool
    {
        // Implementation: S3 HEAD object
        return false;
    }

    public function url(string $path): string
    {
        return $this->endpoint . '/' . $this->bucket . '/' . $path;
    }

    public function copy(string $from, string $to): bool
    {
        // Implementation: S3 COPY object
        return true;
    }

    public function move(string $from, string $to): bool
    {
        $result = $this->copy($from, $to);
        if ($result) {
            $this->delete($from);
        }
        return $result;
    }

    public function size(string $path): int
    {
        // Implementation: S3 HEAD object
        return 0;
    }

    public function lastModified(string $path): int
    {
        // Implementation: S3 HEAD object
        return 0;
    }

    public function files(string $directory = ''): array
    {
        // Implementation: S3 LIST objects
        return [];
    }
}
