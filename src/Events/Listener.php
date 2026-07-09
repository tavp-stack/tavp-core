<?php

declare(strict_types=1);

namespace Tavp\Core\Events;

/**
 * Base listener class — extend this for custom event listeners.
 */
abstract class Listener
{
    /**
     * Handle the event.
     */
    abstract public function handle(mixed $payload, string $event): mixed;
}
