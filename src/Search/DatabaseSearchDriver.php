<?php

declare(strict_types=1);

namespace Tavp\Search;

/**
 * Database fallback search driver (LIKE queries).
 */
class DatabaseSearchDriver implements SearchDriver
{
    public function __construct(array $config)
    {
    }

    public function search(string $index, string $query, array $options = []): array
    {
        // Implementation: use LIKE queries on database
        return [];
    }

    public function index(string $index, string $id, array $data): bool
    {
        // Implementation: store in database for full-text search
        return true;
    }

    public function delete(string $index, string $id): bool
    {
        return true;
    }

    public function createIndex(string $index, array $settings = []): bool
    {
        return true;
    }
}
