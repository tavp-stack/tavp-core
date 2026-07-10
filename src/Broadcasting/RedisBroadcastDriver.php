<?php

declare(strict_types=1);

namespace Tavp\Core\Broadcasting;

/**
 * Redis broadcast driver using Pub/Sub.
 */
class RedisBroadcastDriver implements BroadcastDriver
{
    private ?object $redis = null;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    private function connect(): void
    {
        if ($this->redis === null && class_exists('Redis')) {
            $this->redis = new \Redis();
            $this->redis->connect(
                $this->config['host'] ?? '127.0.0.1',
                $this->config['port'] ?? 6379
            );
        }
    }

    public function broadcast(string $channel, string $event, array $data): bool
    {
        $this->connect();

        $message = json_encode([
            'event' => $event,
            'data' => $data,
            'channel' => $channel,
        ]);

        return $this->redis->publish("broadcast:{$channel}", $message);
    }
}
