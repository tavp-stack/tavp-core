<?php

declare(strict_types=1);

use Tavp\Core\Events\EventDispatcher;
use PHPUnit\Framework\TestCase;

class EventsTest extends TestCase
{
    public function testListenerIsCalledOnDispatch(): void
    {
        $dispatcher = new EventDispatcher();
        $called = false;
        $dispatcher->listen('user.registered', function ($payload) use (&$called) {
            $called = $payload['id'] ?? false;
        });

        $dispatcher->dispatch('user.registered', ['id' => 7]);
        $this->assertSame(7, $called);
    }

    public function testUnknownEventDoesNotError(): void
    {
        $dispatcher = new EventDispatcher();
        $dispatcher->dispatch('nothing');
        $this->assertEmpty($dispatcher->getListeners('nothing'));
    }
}
