<?php

declare(strict_types=1);

use Tavp\Core\Modules\ModuleRegistry;
use PHPUnit\Framework\TestCase;

class ModuleRegistryTest extends TestCase
{
    public function testRegisterAndEnable(): void
    {
        $reg = new ModuleRegistry();
        $reg->register('blog', 'BlogProvider');
        $reg->enable('blog');
        $this->assertTrue($reg->isEnabled('blog'));
        $this->assertSame(['BlogProvider'], $reg->getEnabledProviders());
    }

    public function testResolvesDependencyOrder(): void
    {
        $reg = new ModuleRegistry();
        $deps = [
            'theme' => [],
            'blog' => ['theme'],
            'store' => ['theme'],
            'app' => ['blog', 'store'],
        ];
        $order = $reg->resolveOrder($deps);
        $this->assertLessThan(array_search('app', $order), array_search('blog', $order));
        $this->assertLessThan(array_search('app', $order), array_search('store', $order));
    }

    public function testDetectsCircularDependency(): void
    {
        $reg = new ModuleRegistry();
        $deps = ['a' => ['b'], 'b' => ['a']];
        $this->expectException(\RuntimeException::class);
        $reg->resolveOrder($deps);
    }
}
