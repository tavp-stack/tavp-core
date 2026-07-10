<?php

declare(strict_types=1);

namespace Tavp\Core\Api;

/**
 * API freeze manager — track API stability.
 */
class ApiFreezeManager
{
    private array $publicApi = [];
    private string $freezeDate;

    /**
     * Register public API endpoints.
     */
    public function registerEndpoint(string $method, string $path, array $options = []): void
    {
        $this->publicApi[] = [
            'method' => $method,
            'path' => $path,
            'version' => $options['version'] ?? '1.0',
            'stability' => $options['stability'] ?? 'stable',
            'deprecated' => $options['deprecated'] ?? false,
            'removed_in' => $options['removed_in'] ?? null,
        ];
    }

    /**
     * Get all public API endpoints.
     */
    public function getEndpoints(): array
    {
        return $this->publicApi;
    }

    /**
     * Check if API is frozen.
     */
    public function isFrozen(): bool
    {
        return !empty($this->freezeDate);
    }

    /**
     * Freeze the API.
     */
    public function freeze(): void
    {
        $this->freezeDate = date('Y-m-d');
    }

    /**
     * Get API compatibility report.
     */
    public function getCompatibilityReport(array $oldApi, array $newApi): array
    {
        $breaking = [];
        $added = [];
        $deprecated = [];

        $oldPaths = array_column($oldApi, null, fn($e) => $e['method'] . ' ' . $e['path']);
        $newPaths = array_column($newApi, null, fn($e) => $e['method'] . ' ' . $e['path']);

        foreach ($oldPaths as $key => $endpoint) {
            if (!isset($newPaths[$key])) {
                $breaking[] = $endpoint;
            }
        }

        foreach ($newPaths as $key => $endpoint) {
            if (!isset($oldPaths[$key])) {
                $added[] = $endpoint;
            }
        }

        return [
            'breaking_changes' => $breaking,
            'new_endpoints' => $added,
            'is_compatible' => empty($breaking),
        ];
    }
}
