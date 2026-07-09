<?php

declare(strict_types=1);

namespace Tavp\Core\Queue;

/**
 * Database-backed queue.
 */
class DatabaseQueue implements QueueStore
{
    private string $table;
    private string $connection;

    public function __construct(array $config)
    {
        $this->table = $config['table'] ?? 'jobs';
        $this->connection = $config['connection'] ?? 'default';
    }

    public function push(string $jobClass, mixed $data = null): string
    {
        $id = $this->insertJob($jobClass, $data, 0);
        return $id;
    }

    public function later(int $delay, string $jobClass, mixed $data = null): string
    {
        $availableAt = time() + $delay;
        $id = $this->insertJob($jobClass, $data, $availableAt);
        return $id;
    }

    public function pop(): ?object
    {
        // Fetch next available job and mark as reserved
        $sql = "SELECT * FROM {$this->table} WHERE available_at <= ? AND reserved_at IS NULL ORDER BY id ASC LIMIT 1";
        // Implementation would use database connection
        return null;
    }

    public function size(): int
    {
        $sql = "SELECT COUNT(*) FROM {$this->table}";
        // Implementation would use database connection
        return 0;
    }

    private function insertJob(string $jobClass, mixed $data, int $availableAt): string
    {
        $id = uniqid('job_', true);
        // Implementation would insert into database
        return $id;
    }
}
