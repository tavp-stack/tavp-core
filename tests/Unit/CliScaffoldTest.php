<?php

declare(strict_types=1);

namespace Tavp\Core\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Tavp\Core\Console\MakeModelCommand;
use Tavp\Core\Console\MakeControllerCommand;
use Tavp\Core\Console\MakeMigrationCommand;

class CliScaffoldTest extends TestCase
{
    public function test_make_model_creates_file(): void
    {
        $command = new MakeModelCommand();
        ob_start();
        $command->handle(['Post']);
        $output = ob_get_clean();

        $this->assertStringContainsString('Created', $output);
    }

    public function test_make_controller_creates_file(): void
    {
        $command = new MakeControllerCommand();
        ob_start();
        $command->handle(['Blog']);
        $output = ob_get_clean();

        $this->assertStringContainsString('Created', $output);
    }

    public function test_make_migration_creates_file(): void
    {
        $command = new MakeMigrationCommand();
        ob_start();
        $command->handle(['CreatePostsTable']);
        $output = ob_get_clean();

        $this->assertStringContainsString('Created', $output);
    }

    public function test_make_model_requires_name(): void
    {
        $command = new MakeModelCommand();
        ob_start();
        $command->handle([]);
        $output = ob_get_clean();

        $this->assertStringContainsString('Usage', $output);
    }
}
