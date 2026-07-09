<?php

declare(strict_types=1);

namespace Tavp\Broadcasting;

/**
 * Soketi broadcast driver (open-source Pusher alternative).
 */
class SoketiDriver extends PusherDriver
{
    public function __construct(array $config)
    {
        parent::__construct($config);
        // Soketi uses same protocol as Pusher
    }
}
