<?php

declare(strict_types=1);

namespace Tavp\Core\Events;

/**
 * Event dispatcher — fire events and notify listeners.
 */
class EventDispatcher
{
    private array $listeners = [];
    private array $events = [];

    /**
     * Register a listener for an event.
     */
    public function listen(string $event, callable $listener): void
    {
        $this->listeners[$event][] = $listener;
    }

    /**
     * Register a listener class.
     */
    public function subscribe(string $event, string $listenerClass): void
    {
        $this->listeners[$event][] = new $listenerClass();
    }

    /**
     * Fire an event and notify all listeners.
     */
    public function dispatch(string $event, mixed $payload = null): mixed
    {
        $this->events[$event][] = [
            'time' => microtime(true),
            'payload' => $payload,
        ];

        if (!isset($this->listeners[$event])) {
            return $payload;
        }

        foreach ($this->listeners[$event] as $listener) {
            if (is_callable($listener)) {
                $result = $listener($payload, $event);
                if ($result !== null) {
                    $payload = $result;
                }
            } elseif (is_object($listener) && method_exists($listener, 'handle')) {
                $result = $listener->handle($payload, $event);
                if ($result !== null) {
                    $payload = $result;
                }
            }
        }

        return $payload;
    }

    /**
     * Check if any listeners are registered for an event.
     */
    public function hasListeners(string $event): bool
    {
        return !empty($this->listeners[$event]);
    }

    /**
     * Get all listeners for an event.
     */
    public function getListeners(string $event): array
    {
        return $this->listeners[$event] ?? [];
    }

    /**
     * Remove all listeners for an event.
     */
    public function forget(string $event): void
    {
        unset($this->listeners[$event]);
    }

    /**
     * Remove all listeners.
     */
    public function forgetAll(): void
    {
        $this->listeners = [];
    }

    /**
     * Get fired events log.
     */
    public function getEvents(): array
    {
        return $this->events;
    }
}
