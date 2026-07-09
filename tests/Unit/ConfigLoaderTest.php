<?php

declare(strict_types=1);

use Tavp\Core\Config\ConfigLoader;
use PHPUnit\Framework\TestCase;

/**
 * Tests config loading and dot-notation access. Pure PHP.
 */
class ConfigLoaderTest extends TestCase
{
    public function testLoadsDirectoryAndReadsValue(): void
    {
        $loader = new ConfigLoader();
        $dir = sys_get_temp_dir() . '/tavp_config_' . uniqid();
        mkdir($dir);
        file_put_contents($dir . '/app.php', "<?php return ['name' => 'TAVP', 'env' => 'local'];");

        $loader->loadDirectory($dir);
        $this->assertSame('TAVP', $loader->get('app.name'));
        $this->assertSame('local', $loader->get('app.env'));

        array_map('unlink', glob($dir . '/*'));
        rmdir($dir);
    }

    public function testReturnsDefaultWhenMissing(): void
    {
        $loader = new ConfigLoader();
        $this->assertSame('fallback', $loader->get('missing.key', 'fallback'));
    }
}
