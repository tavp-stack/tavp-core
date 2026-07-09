<?php

declare(strict_types=1);

namespace Tavp\Core\Tests\Unit;

use PHPUnit\Framework\TestCase;

class GitTest extends TestCase
{
    public function test_git_status_class_exists(): void
    {
        $this->assertTrue(class_exists('Tavp\Git\GitStatus'));
    }

    public function test_remote_add_class_exists(): void
    {
        $this->assertTrue(class_exists('Tavp\Git\RemoteAddCommand'));
    }

    public function test_pull_command_class_exists(): void
    {
        $this->assertTrue(class_exists('Tavp\Git\PullCommand'));
    }

    public function test_push_command_class_exists(): void
    {
        $this->assertTrue(class_exists('Tavp\Git\PushCommand'));
    }

    public function test_init_command_class_exists(): void
    {
        $this->assertTrue(class_exists('Tavp\Git\InitCommand'));
    }
}
