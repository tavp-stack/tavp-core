<?php

declare(strict_types=1);

namespace Tavp\Core\Console;

/**
 * tavp db:seed — run database seeders.
 *
 * Usage: tavp db:seed [--class=SeederName]
 */
class SeedCommand
{
    private string $seedsPath;

    public function __construct()
    {
        $this->seedsPath = base_path('database/seeds');
    }

    public function handle(array $args): void
    {
        $targetClass = $this->extractOption($args, '--class', null);

        if (!is_dir($this->seedsPath)) {
            echo "Seeds directory not found: {$this->seedsPath}\n";
            return;
        }

        $files = glob($this->seedsPath . '/*Seeder.php');

        if (empty($files)) {
            echo "No seeders found.\n";
            return;
        }

        $run = 0;
        foreach ($files as $file) {
            $className = pathinfo($file, PATHINFO_FILENAME);

            if ($targetClass !== null && $className !== $targetClass) {
                continue;
            }

            require_once $file;

            $fqcn = "Database\\Seeds\\{$className}";

            if (!class_exists($fqcn)) {
                // Try without namespace
                $seeder = new $className();
            } else {
                $seeder = new $fqcn();
            }

            if (method_exists($seeder, 'run')) {
                echo "  Seeding: {$className}\n";
                $seeder->run();
                $run++;
            }
        }

        echo "Seeded {$run} seeder(s).\n";
    }

    private function extractOption(array $args, string $key, ?string $default): ?string
    {
        foreach ($args as $arg) {
            if (str_starts_with($arg, $key . '=')) {
                return substr($arg, strlen($key) + 1);
            }
        }

        return $default;
    }
}
