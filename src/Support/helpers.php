<?php

declare(strict_types=1);

// Global helper functions for TAVP Core.
// These are intentionally small and human-readable.

use Tavp\Core\Application;
use Tavp\Core\Http\Response;

if (!function_exists('response')) {
    /**
     * Create a Response, optionally with body content and status code.
     */
    function response(string $content = '', int $status = 200): Response
    {
        return (new Response())->setContent($content)->setStatusCode($status);
    }
}

if (!function_exists('redirect')) {
    /**
     * Create a redirect Response to the given path or URL.
     */
    function redirect(string $path, int $status = 302): Response
    {
        return (new Response())->header('Location', $path)->setStatusCode($status);
    }
}

if (!function_exists('env')) {
    /**
     * Read a value from the loaded .env, falling back to a default.
     */
    function env(string $key, mixed $default = null): mixed
    {
        return Application::getInstance()->getEnv($key, $default);
    }
}

if (!function_exists('config')) {
    /**
     * Read a configuration value using "file.key" dot notation.
     */
    function config(string $key, mixed $default = null): mixed
    {
        return Application::getInstance()->getConfig()->get($key, $default);
    }
}

if (!function_exists('storage_path')) {
    /**
     * Build a path inside the storage directory.
     */
    function storage_path(string $path = ''): string
    {
        $base = Application::getInstance()->getBasePath() . '/storage';

        return $path === '' ? $base : $base . '/' . ltrim($path, '/');
    }
}

if (!function_exists('base_path')) {
    /**
     * Build a path relative to the project root.
     */
    function base_path(string $path = ''): string
    {
        $base = Application::getInstance()->getBasePath();

        return $path === '' ? $base : $base . '/' . ltrim($path, '/');
    }
}

if (!function_exists('app')) {
    /**
     * Get the application instance, or a bound service by name.
     */
    function app(string $service = null): mixed
    {
        $app = Application::getInstance();

        return $service === null ? $app : $app->getService($service);
    }
}

if (!function_exists('route')) {
    /**
     * Generate a URL for a named route.
     *
     * route('home')                     → "/"
     * route('users.show', ['id' => 1])  → "/users/1"
     */
    function route(string $name, array $parameters = []): string
    {
        $router = app('router');

        if ($router === null) {
            return '/';
        }

        /** @var \Tavp\Core\Routing\Router $router */
        $routes = $router->getRoutes();

        foreach ($routes as $route) {
            if (($route['name'] ?? null) !== $name) {
                continue;
            }

            $uri = $route['uri'];

            foreach ($parameters as $key => $value) {
                $uri = str_replace('{' . $key . '}', (string) $value, $uri);
            }

            return $uri;
        }

        return '/';
    }
}

if (!function_exists('view')) {
    /**
     * Render a view template from a controller or anywhere.
     */
    function view(string $template, array $data = []): string
    {
        $viewFactory = app('view');

        if ($viewFactory === null) {
            return "<!-- View service not available: {$template} -->";
        }

        /** @var \Tavp\Core\View\ViewFactory $viewFactory */
        return $viewFactory->render($template, $data);
    }
}
