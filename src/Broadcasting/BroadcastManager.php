<?php

declare(strict_types=1);

namespace Tavp\Core\Broadcasting;

/**
 * Broadcasting manager — driver-based real-time messaging.
 */
class BroadcastManager
{
    private array $drivers = [];
    private string $defaultDriver;

    public function __construct(array $config)
    {
        $this->defaultDriver = $config['default'] ?? 'log';

        foreach ($config['connections'] ?? [] as $name => $connectionConfig) {
            $this->drivers[$name] = match ($connectionConfig['driver'] ?? 'log') {
                'pusher' => new PusherDriver($connectionConfig),
                'soketi' => new SoketiDriver($connectionConfig),
                'redis' => new RedisBroadcastDriver($connectionConfig),
                'log' => new LogBroadcastDriver($connectionConfig),
                default => new LogBroadcastDriver($connectionConfig),
            };
        }
    }

    /**
     * Broadcast a message to a channel.
     */
    public function broadcast(string $channel, string $event, array $data): bool
    {
        return $this->driver()->broadcast($channel, $event, $data);
    }

    /**
     * Get the broadcast driver.
     */
    private function driver(?string $name = null): BroadcastDriver
    {
        $driver = $name ?? $this->defaultDriver;
        return $this->drivers[$driver] ?? $this->drivers[$this->defaultDriver];
    }
}

interface BroadcastDriver
{
    public function broadcast(string $channel, string $event, array $data): bool;
}
