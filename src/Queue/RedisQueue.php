<?php

declare(strict_types=1);

namespace Tavp\Core\Queue;

/**
 * Redis-backed queue.
 */
class RedisQueue implements QueueStore
{
    private ?object $redis = null;
    private string $queueName;

    public function __construct(array $config)
    {
        $this->queueName = $config['queue'] ?? 'tavp:jobs';
    }

    private function connect(): void
    {
        if ($this->redis === null && class_exists('Redis')) {
            $this->redis = new \Redis();
            $this->redis->connect('127.0.0.1', 6379);
        }
    }

    public function push(string $jobClass, mixed $data = null): string
    {
        $this->connect();
        $id = uniqid('job_', true);
        $job = serialize(['id' => $id, 'job' => $jobClass, 'data' => $data, 'created_at' => time()]);
        $this->redis->rPush($this->queueName, $job);
        return $id;
    }

    public function later(int $delay, string $jobClass, mixed $data = null): string
    {
        $this->connect();
        $id = uniqid('job_', true);
        $job = serialize(['id' => $id, 'job' => $jobClass, 'data' => $data, 'available_at' => time() + $delay]);
        $this->redis->zAdd($this->queueName . ':delayed', time() + $delay, $job);
        return $id;
    }

    public function pop(): ?object
    {
        $this->connect();
        $job = $this->redis->lPop($this->queueName);
        return $job ? unserialize($job) : null;
    }

    public function size(): int
    {
        $this->connect();
        return $this->redis->lLen($this->queueName);
    }
}
