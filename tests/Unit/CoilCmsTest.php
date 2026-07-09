<?php

declare(strict_types=1);

use Tavp\Core\Coil\CoilServer;
use Tavp\Core\Cms\CmsContent;
use PHPUnit\Framework\TestCase;

class CoilCmsTest extends TestCase
{
    public function testCoilStartsInFallbackModeWithoutSwoole(): void
    {
        $server = new CoilServer();
        $result = $server->start();
        // On this machine Swoole is not installed, so it must fall back.
        $this->assertContains($result['mode'], ['swoole', 'fallback']);
    }

    public function testCmsBlocksAndCollections(): void
    {
        $cms = new CmsContent();
        $cms->setBlock('hero', 'Welcome');
        $this->assertSame('Welcome', $cms->getBlock('hero'));

        $cms->addToCollection('features', ['title' => 'Fast']);
        $this->assertCount(1, $cms->getCollection('features'));
        $this->assertSame('Fast', $cms->getCollection('features')[0]['title']);
    }
}
