<?php

declare(strict_types=1);

namespace Tavp\Core;

use Tavp\Core\Routing\Router;

/**
 * The HTTP kernel handles the request lifecycle:
 *   boot -> resolve route -> dispatch to controller -> respond
 *
 * It is intentionally thin. Heavy lifting (ORM, views) is delegated to
 * the services registered in the Application container.
 */
class Kernel
{
    public function __construct(
        private Application $app,
        private Router $router,
    ) {
    }

    /**
     * Handle an incoming request and return the response body.
     */
    public function handle(string $method, string $uri): string
    {
        $uri = $this->normalizeUri($uri);

        $route = $this->router->match($method, $uri);

        if ($route === null) {
            http_response_code(404);

            return $this->renderNotFound($uri);
        }

        return $this->dispatch($route);
    }

    /**
     * Dispatch a matched route to its controller or closure.
     */
    private function dispatch(array $route): string
    {
        $handler = $route['handler'];

        if (is_callable($handler)) {
            return (string) $handler($route['parameters']);
        }

        if (is_array($handler) && count($handler) === 2) {
            [$controllerClass, $method] = $handler;

            if (!class_exists($controllerClass)) {
                http_response_code(500);

                return "Controller {$controllerClass} not found.";
            }

            $controller = new $controllerClass();
            $result = $controller->$method(...array_values($route['parameters']));

            return (string) $result;
        }

        http_response_code(500);

        return 'Invalid route handler.';
    }

    private function normalizeUri(string $uri): string
    {
        $uri = parse_url($uri, PHP_URL_PATH) ?: '/';

        return $uri === '' ? '/' : $uri;
    }

    private function renderNotFound(string $uri): string
    {
        return "404 — Page not found: {$uri}";
    }
}
