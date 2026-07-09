<?php

declare(strict_types=1);

namespace Tavp\Core\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Tavp\Core\Http\Middleware\ThrottleRequests;

class ThrottleRequestsTest extends TestCase
{
    public function test_throttle_implements_middleware_interface(): void
    {
        $middleware = new ThrottleRequests();
        $this->assertInstanceOf(\Tavp\Core\Http\Middleware\Middleware::class, $middleware);
    }

    public function test_throttle_allows_requests_under_limit(): void
    {
        $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
        $_SERVER['REQUEST_URI'] = '/test-throttle-' . uniqid();

        $middleware = new ThrottleRequests(10, 60);
        $result = $middleware->handle(fn () => 'passed');

        $this->assertEquals('passed', $result);
    }
}
