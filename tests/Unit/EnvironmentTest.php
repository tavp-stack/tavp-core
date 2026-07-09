<?php

declare(strict_types=1);

use Tavp\Core\Environment\EnvironmentDetector;
use Tavp\Core\Environment\EnvironmentLoader;
use PHPUnit\Framework\TestCase;

/**
 * Tests environment detection and .env parsing. Pure PHP.
 */
class EnvironmentTest extends TestCase
{
    public function testDetectsProduction(): void
    {
        $d = new EnvironmentDetector();
        $this->assertSame('production', $d->detect('production'));
        $this->assertTrue($d->isProduction($d->detect('prod')));
    }

    public function testDetectsLocalByDefault(): void
    {
        $d = new EnvironmentDetector();
        $this->assertSame('local', $d->detect(null));
        $this->assertSame('local', $d->detect(''));
    }

    public function testDetectsStaging(): void
    {
        $d = new EnvironmentDetector();
        $this->assertSame('staging', $d->detect('staging'));
    }

    public function testEnvLoaderParsesValues(): void
    {
        $loader = new EnvironmentLoader();
        $tmp = tempnam(sys_get_temp_dir(), 'env');
        file_put_contents($tmp, "APP_NAME=TAVP\nAPP_DEBUG=true\n# comment\n");
        $vars = $loader->load($tmp);
        unlink($tmp);

        $this->assertSame('TAVP', $vars['APP_NAME']);
        $this->assertSame('1', $vars['APP_DEBUG']);
    }

    public function testEnvLoaderReturnsEmptyForMissingFile(): void
    {
        $loader = new EnvironmentLoader();
        $this->assertSame([], $loader->load('/path/that/does/not/exist/.env'));
    }
}
