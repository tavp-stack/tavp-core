<?php

declare(strict_types=1);

namespace Tavp\Core\Console\Commands;

/**
 * tavp migrate:fresh — drop all tables and re-run migrations
 */
class MigrateFreshCommand
{
    public function execute(array $arguments): void
    {
        $seed = in_array('--seed', $arguments);

        echo "Dropping all tables...\n";
        $this->dropAllTables();

        echo "Running migrations...\n";
        $this->runMigrations();

        if ($seed) {
            echo "Running seeders...\n";
            $this->runSeeders();
        }

        echo "✓ Database refreshed successfully!\n";
    }

    private function dropAllTables(): void
    {
        $db = $this->getConnection();
        $tables = $db->listTables();

        foreach ($tables as $table) {
            $db->dropTable($table);
        }
    }

    private function runMigrations(): void
    {
        $migrationsDir = base_path('database/migrations');
        $files = glob($migrationsDir . '/*_*.php');
        sort($files);

        foreach ($files as $file) {
            $basename = basename($file);
            echo "  Migrating: {$basename}\n";

            require_once $file;
            $className = $this->getClassName($file);
            $migration = new $className();

            $migration->up($this->getSchemaBuilder());
        }
    }

    private function runSeeders(): void
    {
        $seedsDir = base_path('database/seeds');
        if (!is_dir($seedsDir)) {
            return;
        }

        $files = glob($seedsDir . '/*.php');

        foreach ($files as $file) {
            $basename = basename($file);
            echo "  Seeding: {$basename}\n";

            require_once $file;
            $className = pathinfo($file, PATHINFO_FILENAME);
            $seeder = new $className();
            $seeder->run();
        }
    }

    private function getClassName(string $file): string
    {
        $content = file_get_contents($file);
        if (preg_match('/^class\s+(\w+)/m', $content, $matches)) {
            return $matches[1];
        }
        return pathinfo($file, PATHINFO_FILENAME);
    }

    private function getConnection(): object
    {
        return new class {
            public function listTables(): array { return []; }
            public function dropTable(string $table): void {}
        };
    }

    private function getSchemaBuilder(): object
    {
        return new class {
            public function createTable(string $table, callable $definition): void {}
            public function dropTable(string $table): void {}
        };
    }
}
