<?php

declare(strict_types=1);

namespace Tavp\Core\Events;

/**
 * Event service provider — register event listeners.
 */
class EventServiceProvider
{
    /**
     * Register event listeners.
     */
    public function register(): void
    {
        $dispatcher = $this->app->get('events');

        $listeners = $this->getListeners();

        foreach ($listeners as $event => $listenerClass) {
            $dispatcher->listen($event, new $listenerClass());
        }
    }

    /**
     * Define event-to-listener mappings.
     */
    protected function getListeners(): array
    {
        return [
            // 'user.registered' => UserRegisteredListener::class,
            // 'user.login' => UserLoginListener::class,
        ];
    }
}
