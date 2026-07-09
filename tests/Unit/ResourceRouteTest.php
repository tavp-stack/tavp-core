<?php

declare(strict_types=1);

namespace Tavp\Core\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Tavp\Core\Routing\Router;

class ResourceRouteTest extends TestCase
{
    private Router $router;

    protected function setUp(): void
    {
        $this->router = new Router();
    }

    public function test_resource_registers_all_crud_routes(): void
    {
        $this->router->resource('posts', 'PostController');

        $routes = $this->router->getRoutes();
        $this->assertCount(7, $routes);

        $methods = array_column($routes, 'method');
        $this->assertContains('GET', $methods);
        $this->assertContains('POST', $methods);
        $this->assertContains('PUT', $methods);
        $this->assertContains('DELETE', $methods);
    }

    public function test_resource_routes_match_correctly(): void
    {
        $this->router->resource('users', 'UserController');

        $route = $this->router->match('GET', '/users');
        $this->assertNotNull($route);

        $route = $this->router->match('GET', '/users/123');
        $this->assertNotNull($route);
        $this->assertEquals('123', $route['parameters']['id']);

        $route = $this->router->match('POST', '/users');
        $this->assertNotNull($route);

        $route = $this->router->match('PUT', '/users/123');
        $this->assertNotNull($route);

        $route = $this->router->match('DELETE', '/users/123');
        $this->assertNotNull($route);
    }
}
