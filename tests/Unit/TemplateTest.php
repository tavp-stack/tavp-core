<?php

declare(strict_types=1);

namespace Tavp\Core\Tests\Unit;

use PHPUnit\Framework\TestCase;

class TemplateTest extends TestCase
{
    public function test_dashboard_view_exists(): void
    {
        $file = base_path('resources/views/dashboard.volt');
        $this->assertFileExists($file);
    }

    public function test_home_view_exists(): void
    {
        $file = base_path('resources/views/home.volt');
        $this->assertFileExists($file);
    }

    public function test_layout_exists(): void
    {
        $file = base_path('resources/views/layouts/app.volt');
        $this->assertFileExists($file);
    }

    public function test_error_pages_exist(): void
    {
        $errorPages = [403, 404, 419, 429, 500, 503];

        foreach ($errorPages as $code) {
            $file = base_path("resources/views/errors/{$code}.volt");
            $this->assertFileExists($file, "Error page {$code}.volt not found");
        }
    }

    public function test_dashboard_contains_required_elements(): void
    {
        $content = file_get_contents(base_path('resources/views/dashboard.volt'));
        $this->assertStringContainsString('Dashboard', $content);
        $this->assertStringContainsString('layout', $content);
    }

    public function test_home_contains_required_elements(): void
    {
        $content = file_get_contents(base_path('resources/views/home.volt'));
        $this->assertStringContainsString('TAVP', $content);
    }
}
