<?php

declare(strict_types=1);

namespace Tavp\Search;

/**
 * Search manager — driver-based search abstraction.
 */
class SearchManager
{
    private array $drivers = [];
    private string $defaultDriver;

    public function __construct(array $config)
    {
        $this->defaultDriver = $config['default'] ?? 'database';

        foreach ($config['connections'] ?? [] as $name => $connectionConfig) {
            $this->drivers[$name] = match ($connectionConfig['driver'] ?? 'database') {
                'meilisearch' => new MeilisearchDriver($connectionConfig),
                'elasticsearch' => new ElasticsearchDriver($connectionConfig),
                'database' => new DatabaseSearchDriver($connectionConfig),
                default => new DatabaseSearchDriver($connectionConfig),
            };
        }
    }

    /**
     * Search for documents.
     */
    public function search(string $index, string $query, array $options = []): array
    {
        return $this->driver()->search($index, $query, $options);
    }

    /**
     * Index a document.
     */
    public function index(string $index, string $id, array $data): bool
    {
        return $this->driver()->index($index, $id, $data);
    }

    /**
     * Delete a document.
     */
    public function delete(string $index, string $id): bool
    {
        return $this->driver()->delete($index, $id);
    }

    /**
     * Create an index.
     */
    public function createIndex(string $index, array $settings = []): bool
    {
        return $this->driver()->createIndex($index, $settings);
    }

    private function driver(?string $name = null): SearchDriver
    {
        $driver = $name ?? $this->defaultDriver;
        return $this->drivers[$driver] ?? $this->drivers[$this->defaultDriver];
    }
}

interface SearchDriver
{
    public function search(string $index, string $query, array $options = []): array;
    public function index(string $index, string $id, array $data): bool;
    public function delete(string $index, string $id): bool;
    public function createIndex(string $index, array $settings = []): bool;
}
