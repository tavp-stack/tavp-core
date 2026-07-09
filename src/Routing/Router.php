<?php

declare(strict_types=1);

namespace Tavp\Core\Routing;

/**
 * A small, human-readable router.
 *
 * Supports the common Laravel-style verbs plus route groups, named
 * routes, and resource routes. Under the hood it does simple pattern
 * matching with no magic — keeping the hot path fast.
 */
class Router
{
    private array $routes = [];
    private array $namedRoutes = [];
    private array $groupStack = [];

    public function get(string $uri, $handler): self
    {
        return $this->addRoute('GET', $uri, $handler);
    }

    public function post(string $uri, $handler): self
    {
        return $this->addRoute('POST', $uri, $handler);
    }

    public function put(string $uri, $handler): self
    {
        return $this->addRoute('PUT', $uri, $handler);
    }

    public function patch(string $uri, $handler): self
    {
        return $this->addRoute('PATCH', $uri, $handler);
    }

    public function delete(string $uri, $handler): self
    {
        return $this->addRoute('DELETE', $uri, $handler);
    }

    /**
     * Register a resource controller: index, show, store, update, destroy.
     */
    public function resource(string $name, string $controller): self
    {
        $base = '/' . trim($name, '/');

        $this->get($base, [$controller, 'index']);
        $this->get($base . '/create', [$controller, 'create']);
        $this->post($base, [$controller, 'store']);
        $this->get($base . '/{id}', [$controller, 'show']);
        $this->get($base . '/{id}/edit', [$controller, 'edit']);
        $this->put($base . '/{id}', [$controller, 'update']);
        $this->delete($base . '/{id}', [$controller, 'destroy']);

        return $this;
    }

    /**
     * Apply a set of attributes (prefix, middleware, name) to a group of routes.
     */
    public function group(array $attributes, callable $callback): self
    {
        $this->groupStack[] = $attributes;
        $callback($this);
        array_pop($this->groupStack);

        return $this;
    }

    public function name(string $name): self
    {
        // Name the most recently added route.
        $last = array_key_last($this->routes);
        if ($last !== null) {
            $this->routes[$last]['name'] = $name;
            $this->namedRoutes[$name] = $this->routes[$last]['uri'];
        }

        return $this;
    }

    private function addRoute(string $method, string $uri, $handler): self
    {
        $uri = $this->applyGroupPrefix($uri);

        $this->routes[] = [
            'method' => $method,
            'uri' => $uri,
            'handler' => $handler,
            'name' => null,
            'middleware' => $this->collectMiddleware(),
        ];

        return $this;
    }

    private function applyGroupPrefix(string $uri): string
    {
        $prefix = '';
        foreach ($this->groupStack as $group) {
            $prefix .= ($group['prefix'] ?? '');
        }

        return '/' . ltrim($prefix . '/' . ltrim($uri, '/'), '/');
    }

    private function collectMiddleware(): array
    {
        $middleware = [];
        foreach ($this->groupStack as $group) {
            if (isset($group['middleware'])) {
                $middleware = array_merge($middleware, (array) $group['middleware']);
            }
        }

        return $middleware;
    }

    /**
     * Find a matching route for the given method and uri.
     */
    public function match(string $method, string $uri): ?array
    {
        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            $parameters = $this->matchUri($route['uri'], $uri);
            if ($parameters !== null) {
                return [
                    'handler' => $route['handler'],
                    'parameters' => $parameters,
                    'middleware' => $route['middleware'],
                ];
            }
        }

        return null;
    }

    /**
     * Match a route pattern against a concrete uri, extracting parameters.
     */
    private function matchUri(string $pattern, string $uri): ?array
    {
        $patternSegments = explode('/', trim($pattern, '/'));
        $uriSegments = explode('/', trim($uri, '/'));

        if (count($patternSegments) !== count($uriSegments)) {
            return null;
        }

        $parameters = [];
        foreach ($patternSegments as $index => $segment) {
            if (str_starts_with($segment, '{') && str_ends_with($segment, '}')) {
                $name = trim($segment, '{}');
                $parameters[$name] = $uriSegments[$index];
                continue;
            }

            if ($segment !== $uriSegments[$index]) {
                return null;
            }
        }

        return $parameters;
    }

    public function getRoutes(): array
    {
        return $this->routes;
    }
}
