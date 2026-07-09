<?php

declare(strict_types=1);

use Tavp\Core\Routing\Router;
use PHPUnit\Framework\TestCase;

/**
 * Tests the router's matching and parameter extraction.
 * Pure PHP — no Phalcon required.
 */
class RouterTest extends TestCase
{
    public function testGetRouteMatches(): void
    {
        $router = new Router();
        $router->get('/posts', fn () => 'list');

        $match = $router->match('GET', '/posts');
        $this->assertNotNull($match);
        $this->assertIsCallable($match['handler']);
    }

    public function testRouteWithParameterExtractsValue(): void
    {
        $router = new Router();
        $router->get('/posts/{id}', fn ($params) => $params);

        $match = $router->match('GET', '/posts/42');
        $this->assertNotNull($match);
        $this->assertSame('42', $match['parameters']['id']);
    }

    public function testWrongMethodDoesNotMatch(): void
    {
        $router = new Router();
        $router->post('/posts', fn () => 'created');

        $this->assertNull($router->match('GET', '/posts'));
    }

    public function testNamedRouteIsRegistered(): void
    {
        $router = new Router();
        $router->get('/about', fn () => 'about')->name('about');

        $routes = $router->getRoutes();
        $last = end($routes);
        $this->assertSame('about', $last['name']);
    }

    public function testGroupPrefixIsApplied(): void
    {
        $router = new Router();
        $router->group(['prefix' => 'admin'], function (Router $r) {
            $r->get('/dashboard', fn () => 'dash');
        });

        $match = $router->match('GET', '/admin/dashboard');
        $this->assertNotNull($match);
    }
}
