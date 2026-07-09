<?php

declare(strict_types=1);

namespace Tavp\Core\Events;

/**
 * A simple, readable event dispatcher.
 *
 * Listeners are registered by event name and invoked in registration
 * order. Used by the framework and by modules to decouple logic.
 */
class EventDispatcher
{
    private array $listeners = [];

    /**
     * Register a listener for an event name.
     */
    public function listen(string $event, callable $callback): void
    {
        $this->listeners[$event][] = $callback;
    }

    /**
     * Dispatch an event, calling every registered listener.
     * The payload is passed to each listener.
     */
    public function dispatch(string $event, mixed $payload = null): void
    {
        foreach ($this->listeners[$event] ?? [] as $callback) {
            $callback($payload);
        }
    }

    /**
     * Return the listeners registered for an event (for debugging).
     */
    public function getListeners(string $event): array
    {
        return $this->listeners[$event] ?? [];
    }
}
