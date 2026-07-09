<?php

declare(strict_types=1);

namespace Tavp\Core\Environment;

/**
 * Loads environment variables from a .env file into a simple key/value map.
 *
 * This is a tiny, dependency-free parser. It does not overwrite variables
 * that already exist in the real environment (e.g. from the OS or Lando).
 */
class EnvironmentLoader
{
    /**
     * Parse a .env file and return its variables as an associative array.
     */
    public function load(string $filePath): array
    {
        if (!is_file($filePath)) {
            return [];
        }

        $variables = [];
        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $line) {
            $line = trim($line);

            // Skip comments and blank lines.
            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }

            // Only handle KEY=VALUE pairs.
            if (!str_contains($line, '=')) {
                continue;
            }

            [$rawKey, $rawValue] = explode('=', $line, 2);
            $key = trim($rawKey);
            $value = $this->cleanValue(trim($rawValue));

            $variables[$key] = $value;
        }

        return $variables;
    }

    /**
     * Remove surrounding quotes and map common boolean/null strings.
     */
    private function cleanValue(string $value): string
    {
        $value = trim($value, "\"'");

        return match (strtolower($value)) {
            'true' => '1',
            'false' => '0',
            'null', '' => '',
            default => $value,
        };
    }
}
