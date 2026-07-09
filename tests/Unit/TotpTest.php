<?php

declare(strict_types=1);

use Tavp\Core\Auth\Totp\TotpService;
use PHPUnit\Framework\TestCase;

class TotpTest extends TestCase
{
    public function testGeneratedCodeVerifies(): void
    {
        $service = new TotpService('JBSWY3DPEHPK3PXP');
        $code = $service->generate();
        $this->assertSame(6, strlen($code));
        $this->assertTrue($service->verify($code));
    }

    public function testWrongCodeFails(): void
    {
        $service = new TotpService('JBSWY3DPEHPK3PXP');
        $this->assertFalse($service->verify('000000'));
    }

    public function testProvisioningUriContainsSecret(): void
    {
        $service = new TotpService('JBSWY3DPEHPK3PXP');
        $uri = $service->provisioningUri('user@example.com');
        $this->assertStringContainsString('otpauth://totp/', $uri);
        $this->assertStringContainsString('JBSWY3DPEHPK3PXP', $uri);
    }
}
