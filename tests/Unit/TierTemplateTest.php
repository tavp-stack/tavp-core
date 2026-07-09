<?php

declare(strict_types=1);

use Tavp\Core\Templates\WebsiteTemplate;
use Tavp\Core\Templates\ApplicationTemplate;
use Tavp\Core\Templates\EnterpriseTemplate;
use PHPUnit\Framework\TestCase;

/**
 * Tests the tier template definitions. Pure PHP.
 */
class TierTemplateTest extends TestCase
{
    public function testWebsiteHasNoDatabaseOrAuth(): void
    {
        $t = new WebsiteTemplate();
        $this->assertFalse($t->usesDatabase());
        $this->assertFalse($t->usesAuth());
        $this->assertNotEmpty($t->files());
    }

    public function testApplicationHasDatabaseAndAuth(): void
    {
        $t = new ApplicationTemplate();
        $this->assertTrue($t->usesDatabase());
        $this->assertTrue($t->usesAuth());
    }

    public function testEnterpriseExtendsApplicationFiles(): void
    {
        $enterprise = new EnterpriseTemplate();
        $application = new ApplicationTemplate();
        foreach ($application->files() as $file) {
            $this->assertContains($file, $enterprise->files());
        }
        $this->assertContains('.lando.yml', $enterprise->files());
    }
}
