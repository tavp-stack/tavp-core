<?php

declare(strict_types=1);

namespace Tavp\Core\Tests\Integration;

use PHPUnit\Framework\TestCase;

/**
 * Integration test that REQUIRES the Phalcon C-extension.
 *
 * This only runs inside the Lando container (or any environment where
 * ext-phalcon is installed). On a machine without Phalcon it is skipped,
 * so the local unit suite stays green everywhere.
 */
class PhalconBootstrapTest extends TestCase
{
    public function testPhalconExtensionIsLoaded(): void
    {
        if (!extension_loaded('phalcon')) {
            $this->markTestSkipped('Phalcon extension not loaded; run inside Lando.');
        }

        $this->assertTrue(extension_loaded('phalcon'));
        $this->assertTrue(class_exists(\Phalcon\Mvc\Model::class));
    }

    public function testTavpModelExtendsPhalconModel(): void
    {
        if (!extension_loaded('phalcon')) {
            $this->markTestSkipped('Phalcon extension not loaded; run inside Lando.');
        }

        // The TAVP base Model must be a real Phalcon model so it inherits
        // the C-extension speed on the hot path.
        $reflection = new \ReflectionClass(\Tavp\Core\Database\Model::class);
        $this->assertTrue($reflection->isSubclassOf(\Phalcon\Mvc\Model::class));
    }
}
