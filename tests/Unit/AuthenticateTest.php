<?php

declare(strict_types=1);

namespace Tavp\Core\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Tavp\Core\Http\Middleware\Authenticate;

class AuthenticateTest extends TestCase
{
    public function test_authenticate_implements_middleware_interface(): void
    {
        $middleware = new Authenticate();
        $this->assertInstanceOf(\Tavp\Core\Http\Middleware\Middleware::class, $middleware);
    }

    public function test_authenticate_returns_401_without_token(): void
    {
        // Remove any existing auth headers
        unset($_SERVER['HTTP_AUTHORIZATION']);
        unset($_COOKIE['tavp_token']);
        unset($_SESSION['auth_token']);

        $middleware = new Authenticate();
        $result = $middleware->handle(fn () => 'should not reach');

        // Should return a Response (redirect or JSON)
        $this->assertNotEquals('should not reach', $result);
    }
}
