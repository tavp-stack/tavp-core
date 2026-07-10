<?php

declare(strict_types=1);

namespace Tavp\Core\Queue;

use Phalcon\Db\Adapter\AdapterInterface;

/**
 * Database-backed queue. Stores jobs in a SQL table.
 */
class DatabaseQueue implements QueueStore
{
    private string $table;
    private string $queue;
    private int $retryAfter;
    private int $maxTries;
    private AdapterInterface $db;

    public function __construct(array $config, AdapterInterface $db)
    {
        $this->table = $config['table'] ?? 'jobs';
        $this->queue = $config['queue'] ?? 'default';
        $this->retryAfter = $config['retry_after'] ?? 90;
        $this->maxTries = $config['max_tries'] ?? 3;
        $this->db = $db;
    }

    public function push(string $jobClass, mixed $data = null): string
    {
        return $this->insertJob($jobClass, $data, 0);
    }

    public function later(int $delay, string $jobClass, mixed $data = null): string
    {
        $availableAt = time() + $delay;

        return $this->insertJob($jobClass, $data, $availableAt);
    }

    public function pop(): ?object
    {
        $now = date('Y-m-d H:i:s');

        $sql = "SELECT * FROM {$this->table}
                WHERE queue = ?
                  AND available_at <= ?
                  AND reserved_at IS NULL
                  AND attempts < ?
                ORDER BY id ASC
                LIMIT 1";

        $job = $this->db->fetchOne($sql, null, [$this->queue, $now, $this->maxTries]);

        if (!$job) {
            return null;
        }

        $this->db->update($this->table, ['reserved_at' => $now], "id = ?", [$job['id']]);

        return (object) $job;
    }

    public function delete(object $job): void
    {
        $this->db->delete($this->table, "id = ?", [$job->id]);
    }

    public function release(object $job): void
    {
        $this->db->update(
            $this->table,
            ['reserved_at' => null, 'attempts' => $job->attempts + 1],
            "id = ?",
            [$job->id]
        );
    }

    public function failed(object $job, ?string $error = null): void
    {
        $this->db->update(
            $this->table,
            ['failed_at' => date('Y-m-d H:i:s'), 'exception' => $error],
            "id = ?",
            [$job->id]
        );
    }

    public function size(): int
    {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE queue = ? AND reserved_at IS NULL";

        $result = $this->db->fetchOne($sql, null, [$this->queue]);

        return (int) ($result[0] ?? 0);
    }

    public function clear(): int
    {
        $count = $this->size();

        $this->db->delete($this->table, "queue = ? AND reserved_at IS NULL", [$this->queue]);

        return $count;
    }

    private function insertJob(string $jobClass, mixed $data, int $availableAt): string
    {
        $id = $this->db->insert($this->table, [
            'queue' => $this->queue,
            'job_class' => $jobClass,
            'payload' => is_string($data) ? $data : json_encode($data),
            'attempts' => 0,
            'reserved_at' => null,
            'available_at' => date('Y-m-d H:i:s', $availableAt),
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        return (string) $this->db->lastInsertId();
    }
}
