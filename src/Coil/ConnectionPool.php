<?php

declare(strict_types=1);

namespace Tavp\Coil;

/**
 * Swoole connection pool for database and Redis.
 */
class ConnectionPool
{
    private string $type;
    private array $config;
    private int $minConnections;
    private int $maxConnections;
    private array $pool = [];
    private int $currentSize = 0;

    public function __construct(string $type, array $config, int $min = 2, int $max = 10)
    {
        $this->type = $type;
        $this->config = $config;
        $this->minConnections = $min;
        $this->maxConnections = $max;
    }

    /**
     * Get a connection from the pool.
     */
    public function get(): object
    {
        if (!empty($this->pool)) {
            return array_pop($this->pool);
        }

        if ($this->currentSize < $this->maxConnections) {
            $this->currentSize++;
            return $this->createConnection();
        }

        // Wait for available connection
        return $this->waitForConnection();
    }

    /**
     * Return a connection to the pool.
     */
    public function put(object $connection): void
    {
        $this->pool[] = $connection;
    }

    /**
     * Create a new connection.
     */
    private function createConnection(): object
    {
        return match ($this->type) {
            'mysql' => $this->createMysqlConnection(),
            'redis' => $this->createRedisConnection(),
            default => throw new \RuntimeException("Unknown connection type: {$this->type}"),
        };
    }

    private function createMysqlConnection(): object
    {
        $pdo = new \PDO(
            "mysql:host={$this->config['host']};port={$this->config['port']};dbname={$this->config['database']}",
            $this->config['username'],
            $this->config['password']
        );
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        return $pdo;
    }

    private function createRedisConnection(): object
    {
        $redis = new \Redis();
        $redis->connect($this->config['host'], $this->config['port']);
        return $redis;
    }

    private function waitForConnection(): object
    {
        // Simple polling for available connection
        $attempts = 0;
        while ($attempts < 100) {
            if (!empty($this->pool)) {
                return array_pop($this->pool);
            }
            usleep(10000); // 10ms
            $attempts++;
        }
        throw new \RuntimeException("Connection pool exhausted");
    }
}
