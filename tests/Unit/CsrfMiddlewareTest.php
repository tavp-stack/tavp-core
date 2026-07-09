<?php

declare(strict_types=1);

namespace Tavp\Core\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Tavp\Core\Http\Middleware\VerifyCsrfToken;

class VerifyCsrfTokenTest extends TestCase
{
    public function test_csrf_implements_middleware_interface(): void
    {
        $middleware = new VerifyCsrfToken();
        $this->assertInstanceOf(\Tavp\Core\Http\Middleware\Middleware::class, $middleware);
    }

    public function test_get_requests_pass_through(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';

        $middleware = new VerifyCsrfToken();
        $result = $middleware->handle(fn () => 'passed');

        $this->assertEquals('passed', $result);
    }
}
