<?php

declare(strict_types=1);

namespace Tavp\Core\Http;

/**
 * A readable wrapper around the incoming HTTP request.
 *
 * Gives controllers ergonomic access to input data without touching
 * the global $_POST / $_GET superglobals directly.
 */
class Request
{
    private array $input;

    public function __construct()
    {
        $this->input = array_merge($_GET, $_POST);
    }

    /**
     * Get a single input value by key, with an optional default.
     */
    public function input(string $key, mixed $default = null): mixed
    {
        return $this->input[$key] ?? $default;
    }

    /**
     * Get only the specified keys from the input.
     */
    public function only(array $keys): array
    {
        $result = [];
        foreach ($keys as $key) {
            if (array_key_exists($key, $this->input)) {
                $result[$key] = $this->input[$key];
            }
        }

        return $result;
    }

    /**
     * Get all input except the specified keys.
     */
    public function except(array $keys): array
    {
        return array_diff_key($this->input, array_flip($keys));
    }

    /**
     * Check whether an input key is present and not empty.
     */
    public function has(string $key): bool
    {
        return isset($this->input[$key]) && $this->input[$key] !== '';
    }

    /**
     * Get the raw request body (useful for JSON APIs).
     */
    public function body(): array
    {
        $raw = file_get_contents('php://input');

        if ($raw === '') {
            return [];
        }

        $decoded = json_decode($raw, true);

        return is_array($decoded) ? $decoded : [];
    }

    public function method(): string
    {
        return $_SERVER['REQUEST_METHOD'] ?? 'GET';
    }
}
