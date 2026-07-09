<?php

declare(strict_types=1);

use Tavp\Core\View\Blocks\BlockRegistry;
use Tavp\Core\Async\Async;
use Tavp\Core\Debug\DebugToolbar;
use PHPUnit\Framework\TestCase;

class BlocksTest extends TestCase
{
    public function testFifteenCoreBlocksRegistered(): void
    {
        $registry = new BlockRegistry();
        $this->assertCount(15, $registry->all());
        $this->assertTrue($registry->has('Button'));
        $this->assertFalse($registry->has('Chart'));
    }

    public function testModuleCanRegisterBlock(): void
    {
        $registry = new BlockRegistry();
        $registry->register('Chart');
        $this->assertTrue($registry->has('Chart'));
    }

    public function testAsyncAllRunsEveryTask(): void
    {
        $results = Async::all(
            fn () => 1,
            fn () => 2,
            fn () => 3,
        );
        $this->assertSame([1, 2, 3], $results);
    }

    public function testDebugToolbarRenders(): void
    {
        $toolbar = new DebugToolbar();
        $toolbar->addQuery('SELECT 1', 0.5);
        $html = $toolbar->render();
        $this->assertStringContainsString('TAVP Debug', $html);
        $this->assertStringContainsString('1 queries', $html);
    }
}
