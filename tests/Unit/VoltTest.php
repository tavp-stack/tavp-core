<?php

declare(strict_types=1);

namespace Tavp\Core\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Tavp\Core\View\ViewFactory;

class VoltTest extends TestCase
{
    public function test_view_factory_exists(): void
    {
        $viewsPath = base_path('resources/views');
        $compiledPath = storage_path('compiled/volt');

        $factory = new ViewFactory($viewsPath, $compiledPath);
        $this->assertInstanceOf(ViewFactory::class, $factory);
    }

    public function test_view_exists_for_home(): void
    {
        $viewsPath = base_path('resources/views');
        $compiledPath = storage_path('compiled/volt');

        $factory = new ViewFactory($viewsPath, $compiledPath);
        $this->assertTrue($factory->exists('home'));
    }

    public function test_view_exists_for_dashboard(): void
    {
        $viewsPath = base_path('resources/views');
        $compiledPath = storage_path('compiled/volt');

        $factory = new ViewFactory($viewsPath, $compiledPath);
        $this->assertTrue($factory->exists('dashboard'));
    }

    public function test_view_exists_for_layout(): void
    {
        $viewsPath = base_path('resources/views');
        $compiledPath = storage_path('compiled/volt');

        $factory = new ViewFactory($viewsPath, $compiledPath);
        $this->assertTrue($factory->exists('layouts.app'));
    }

    public function test_view_exists_for_error_pages(): void
    {
        $viewsPath = base_path('resources/views');
        $compiledPath = storage_path('compiled/volt');

        $factory = new ViewFactory($viewsPath, $compiledPath);
        $this->assertTrue($factory->exists('errors.404'));
        $this->assertTrue($factory->exists('errors.500'));
        $this->assertTrue($factory->exists('errors.403'));
        $this->assertTrue($factory->exists('errors.503'));
    }

    public function test_nonexistent_view_returns_false(): void
    {
        $viewsPath = base_path('resources/views');
        $compiledPath = storage_path('compiled/volt');

        $factory = new ViewFactory($viewsPath, $compiledPath);
        $this->assertFalse($factory->exists('nonexistent.view'));
    }
}
