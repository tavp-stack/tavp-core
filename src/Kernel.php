<?php

declare(strict_types=1);

namespace Tavp\Core;

use Tavp\Core\Exceptions\ExceptionHandler;
use Tavp\Core\Http\Middleware\Middleware;
use Tavp\Core\Http\Response;
use Tavp\Core\Routing\Router;

/**
 * The HTTP kernel handles the request lifecycle:
 *   boot -> resolve route -> run middleware -> dispatch -> respond
 *
 * It is intentionally thin. Heavy lifting (ORM, views) is delegated to
 * the services registered in the Application container.
 */
class Kernel
{
    private ExceptionHandler $exceptionHandler;

    /** @var array<class-string<Middleware>> Global middleware applied to every route */
    private array $middleware = [];

    public function __construct(
        private Application $app,
        private Router $router,
    ) {
        $this->exceptionHandler = new ExceptionHandler(
            $this->app->config('app.debug', false)
        );
    }

    /**
     * Register global middleware that runs on every request.
     *
     * @param array<class-string<Middleware>> $middleware
     */
    public function pipe(array $middleware): self
    {
        $this->middleware = array_merge($this->middleware, $middleware);

        return $this;
    }

    /**
     * Handle an incoming request and return the response body.
     */
    public function handle(string $method, string $uri): string
    {
        $uri = $this->normalizeUri($uri);

        try {
            $route = $this->router->match($method, $uri);

            if ($route === null) {
                http_response_code(404);

                return $this->renderNotFound($uri);
            }

            $result = $this->dispatch($method, $uri, $route);

            return $result instanceof Response ? $result->send() : (string) $result;
        } catch (\Throwable $e) {
            $response = $this->exceptionHandler->render($e);
            http_response_code($response->getStatusCode());

            return $response->getContent();
        }
    }

    /**
     * Dispatch a matched route through the middleware pipeline.
     */
    private function dispatch(string $method, string $uri, array $route): mixed
    {
        $handler = $route['handler'];
        $routeParams = $route['parameters'];
        $routeMiddleware = $route['middleware'] ?? [];

        // Build the final handler (controller or closure)
        $finalHandler = function () use ($handler, $routeParams) {
            return $this->invokeHandler($handler, $routeParams);
        };

        // Merge global + route middleware
        $allMiddleware = array_merge($this->middleware, $routeMiddleware);

        // Build the pipeline: last middleware wraps the final handler
        $pipeline = $finalHandler;
        foreach (array_reverse($allMiddleware) as $middlewareClass) {
            $pipeline = $this->pipelineStep($middlewareClass, $pipeline);
        }

        // Execute the pipeline. The result may be a string or a Response;
        // the caller (handle) finalizes it.
        return $pipeline();
    }

    /**
     * Create a single step in the middleware pipeline.
     */
    private function pipelineStep(string $middlewareClass, callable $next): callable
    {
        return function () use ($middlewareClass, $next) {
            /** @var Middleware $middleware */
            $middleware = new $middlewareClass();

            return $middleware->handle($next);
        };
    }

    /**
     * Invoke a route handler (closure or [Controller, method]).
     */
    private function invokeHandler($handler, array $parameters): mixed
    {
        if (is_callable($handler)) {
            return $handler($parameters);
        }

        if (is_array($handler) && count($handler) === 2) {
            [$controllerClass, $method] = $handler;

            if (!class_exists($controllerClass)) {
                throw new \RuntimeException("Controller {$controllerClass} not found.");
            }

            $controller = new $controllerClass();

            return $controller->$method(...array_values($parameters));
        }

        throw new \RuntimeException('Invalid route handler.');
    }

    private function normalizeUri(string $uri): string
    {
        $uri = parse_url($uri, PHP_URL_PATH) ?: '/';

        return $uri === '' ? '/' : $uri;
    }

    private function renderNotFound(string $uri): string
    {
        try {
            $view = app('view');
            return $view->render('404');
        } catch (\Throwable) {
            return "404 — Page not found: {$uri}";
        }
    }
}
