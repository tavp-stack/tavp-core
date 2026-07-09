<?php

declare(strict_types=1);

namespace Tavp\Core\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Tavp\Core\Auth\TokenService;

class TokenServiceTest extends TestCase
{
    private TokenService $service;

    protected function setUp(): void
    {
        $this->service = new TokenService('test-secret-key-for-testing');
    }

    public function test_create_token_pair_returns_access_and_refresh(): void
    {
        $tokens = $this->service->createTokenPair(1);

        $this->assertArrayHasKey('access_token', $tokens);
        $this->assertArrayHasKey('refresh_token', $tokens);
        $this->assertNotEmpty($tokens['access_token']);
        $this->assertNotEmpty($tokens['refresh_token']);
    }

    public function test_decode_valid_token(): void
    {
        $tokens = $this->service->createTokenPair(42);
        $payload = $this->service->decode($tokens['access_token']);

        $this->assertNotNull($payload);
        $this->assertEquals(42, $payload['sub']);
        $this->assertEquals('access', $payload['type']);
    }

    public function test_decode_expired_token_returns_null(): void
    {
        // Create a token with 0 TTL (already expired)
        $service = new TokenService('test-secret', 0, 30);
        $tokens = $service->createTokenPair(1);
        $payload = $service->decode($tokens['access_token']);

        $this->assertNull($payload);
    }

    public function test_decode_invalid_signature_returns_null(): void
    {
        $tokens = $this->service->createTokenPair(1);

        // Tamper with the token
        $parts = explode('.', $tokens['access_token']);
        $parts[2] = base64_encode('invalid-signature');
        $tamperedToken = implode('.', $parts);

        $payload = $this->service->decode($tamperedToken);
        $this->assertNull($payload);
    }

    public function test_decode_invalid_token_returns_null(): void
    {
        $this->assertNull($this->service->decode('not-a-valid-token'));
        $this->assertNull($this->service->decode(''));
    }
}
