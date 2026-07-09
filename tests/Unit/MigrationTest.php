<?php

declare(strict_types=1);

namespace Tavp\Core\Tests\Unit;

use PHPUnit\Framework\TestCase;

class MigrationTest extends TestCase
{
    public function test_migration_file_exists(): void
    {
        $migrationsDir = base_path('database/migrations');
        $this->assertDirectoryExists($migrationsDir);

        $files = glob($migrationsDir . '/*_*.php');
        $this->assertNotEmpty($files, 'No migration files found');
    }

    public function test_user_migration_has_required_columns(): void
    {
        $file = base_path('database/migrations/2026_07_09_000001_create_users_table.php');
        $this->assertFileExists($file);

        $content = file_get_contents($file);
        $this->assertStringContainsString('users', $content);
        $this->assertStringContainsString('email', $content);
        $this->assertStringContainsString('name', $content);
    }

    public function test_otp_migration_has_required_columns(): void
    {
        $file = base_path('database/migrations/2026_07_09_000002_create_otp_codes_table.php');
        $this->assertFileExists($file);

        $content = file_get_contents($file);
        $this->assertStringContainsString('otp_codes', $content);
        $this->assertStringContainsString('code', $content);
        $this->assertStringContainsString('expires_at', $content);
    }

    public function test_session_migration_has_required_columns(): void
    {
        $file = base_path('database/migrations/2026_07_09_000003_create_user_sessions_table.php');
        $this->assertFileExists($file);

        $content = file_get_contents($file);
        $this->assertStringContainsString('user_sessions', $content);
        $this->assertStringContainsString('user_id', $content);
        $this->assertStringContainsString('token_hash', $content);
    }

    public function test_migrations_are_valid_php(): void
    {
        $migrationsDir = base_path('database/migrations');
        $files = glob($migrationsDir . '/*_*.php');

        foreach ($files as $file) {
            $content = file_get_contents($file);
            $this->assertNotEmpty($content, "Migration file is empty: {$file}");

            // Check it has up and down methods
            $this->assertStringContainsString('function up', $content, "Missing up() in: {$file}");
            $this->assertStringContainsString('function down', $content, "Missing down() in: {$file}");
        }
    }
}
