<?php

declare(strict_types=1);

namespace Tavp\Core\Config;

/**
 * Loads configuration arrays from the config/ directory and exposes them
 * through simple "file.key" dot-notation access.
 */
class ConfigLoader
{
    private array $items = [];

    /**
     * Load every *.php file inside the given config directory.
     * Each file is expected to return an associative array.
     */
    public function loadDirectory(string $configPath): void
    {
        if (!is_dir($configPath)) {
            return;
        }

        foreach (glob($configPath . '/*.php') ?: [] as $file) {
            $name = basename($file, '.php');
            $this->items[$name] = require $file;
        }
    }

    /**
     * Get a config value using "file.key" notation, or a whole file
     * when only the file name is given.
     */
    public function get(string $key, mixed $default = null): mixed
    {
        if (!str_contains($key, '.')) {
            return $this->items[$key] ?? $default;
        }

        [$file, $path] = explode('.', $key, 2);
        $value = $this->items[$file] ?? [];

        foreach (explode('.', $path) as $segment) {
            if (!is_array($value) || !array_key_exists($segment, $value)) {
                return $default;
            }
            $value = $value[$segment];
        }

        return $value;
    }

    public function all(): array
    {
        return $this->items;
    }
}
