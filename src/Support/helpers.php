<?php

declare(strict_types=1);

// Global helper functions for TAVP Core.
// These are intentionally small and human-readable.

use Tavp\Core\Application;

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
