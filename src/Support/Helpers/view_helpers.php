<?php

declare(strict_types=1);

// View & request helpers available to controllers and Volt templates.

use Tavp\Core\Application;

if (!function_exists('asset')) {
    /**
     * Build a URL to a public asset, with cache-busting in production.
     */
    function asset(string $path): string
    {
        $base = rtrim(config('app.url', ''), '/');

        return $base . '/' . ltrim($path, '/');
    }
}

if (!function_exists('url')) {
    /**
     * Build an application URL from a path.
     */
    function url(string $path = ''): string
    {
        $base = rtrim(config('app.url', ''), '/');

        return $path === '' ? $base : $base . '/' . ltrim($path, '/');
    }
}

if (!function_exists('csrf_token')) {
    /**
     * Return (and persist) the current CSRF token.
     */
    function csrf_token(): string
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION['_csrf_token'])) {
            $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['_csrf_token'];
    }
}

if (!function_exists('csrf_field')) {
    /**
     * Return a hidden input field containing the CSRF token.
     */
    function csrf_field(): string
    {
        return '<input type="hidden" name="_token" value="' . csrf_token() . '">';
    }
}

if (!function_exists('old')) {
    /**
     * Return the previously submitted value for a field (after redirect).
     */
    function old(string $key, mixed $default = ''): mixed
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        return $_SESSION['_old_input'][$key] ?? $default;
    }
}

if (!function_exists('session')) {
    /**
     * Get or set a session value.
     */
    function session(string $key, mixed $value = null): mixed
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if ($value === null) {
            return $_SESSION[$key] ?? null;
        }

        $_SESSION[$key] = $value;

        return $value;
    }
}

if (!function_exists('flash')) {
    /**
     * Store a flash message for the next request.
     */
    function flash(string $type, string $message): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION['_flash'][$type] = $message;
    }
}
