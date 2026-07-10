<?php

declare(strict_types=1);

namespace Tavp\Core\Queue;

/**
 * Queue manager — driver-based queue abstraction.
 */
class QueueManager
{
    private array $drivers = [];
    private string $defaultDriver;

    public function __construct(array $config, ?\Phalcon\Db\Adapter\AdapterInterface $db = null)
    {
        $this->defaultDriver = $config['default'] ?? 'database';

        foreach ($config['connections'] ?? [] as $name => $connectionConfig) {
            $this->drivers[$name] = match ($connectionConfig['driver'] ?? 'database') {
                'database' => new DatabaseQueue($connectionConfig, $db),
                'redis' => new RedisQueue($connectionConfig),
                default => new DatabaseQueue($connectionConfig, $db),
            };
        }
    }

    /**
     * Push a job to the queue.
     */
    public function push(string $jobClass, mixed $data = null, ?string $queue = null): string
    {
        return $this->driver($queue)->push($jobClass, $data);
    }

    /**
     * Push a job after a delay.
     */
    public function later(int $delay, string $jobClass, mixed $data = null, ?string $queue = null): string
    {
        return $this->driver($queue)->later($delay, $jobClass, $data);
    }

    /**
     * Get the next job from the queue.
     */
    public function pop(?string $queue = null): ?object
    {
        return $this->driver($queue)->pop();
    }

    /**
     * Get queue size.
     */
    public function size(?string $queue = null): int
    {
        return $this->driver($queue)->size();
    }

    private function driver(?string $name = null): QueueStore
    {
        $driver = $name ?? $this->defaultDriver;
        return $this->drivers[$driver] ?? $this->drivers[$this->defaultDriver];
    }
}

interface QueueStore
{
    public function push(string $jobClass, mixed $data = null): string;
    public function later(int $delay, string $jobClass, mixed $data = null): string;
    public function pop(): ?object;
    public function size(): int;
}
