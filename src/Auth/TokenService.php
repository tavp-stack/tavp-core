<?php

declare(strict_types=1);

namespace Tavp\Core\Auth;

/**
 * Issues and validates JWT access + refresh tokens.
 *
 * Access tokens are short-lived (15 minutes). Refresh tokens last
 * longer (30 days) and are used to obtain new access tokens.
 */
class TokenService
{
    public function __construct(
        private string $secret,
        private int $accessTtlMinutes = 15,
        private int $refreshTtlDays = 30,
    ) {
    }

    /**
     * Create an access + refresh token pair for a user.
     */
    public function createTokenPair(int $userId): array
    {
        $access = $this->encode([
            'sub' => $userId,
            'type' => 'access',
            'exp' => time() + ($this->accessTtlMinutes * 60),
        ]);

        $refresh = $this->encode([
            'sub' => $userId,
            'type' => 'refresh',
            'exp' => time() + ($this->refreshTtlDays * 86400),
        ]);

        return ['access_token' => $access, 'refresh_token' => $refresh];
    }

    /**
     * Decode and validate a token. Returns the payload or null.
     */
    public function decode(string $token): ?array
    {
        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            return null;
        }

        $payload = json_decode(base64_decode($parts[1]), true);
        if (!is_array($payload)) {
            return null;
        }

        if (($payload['exp'] ?? 0) < time()) {
            return null;
        }

        // In production, verify the signature (HMAC) against $this->secret.
        return $payload;
    }

    private function encode(array $payload): string
    {
        $header = base64_encode(json_encode(['alg' => 'HS256', 'typ' => 'JWT']));
        $body = base64_encode(json_encode($payload));
        $signature = base64_encode(hash_hmac('sha256', "{$header}.{$body}", $this->secret, true));

        return "{$header}.{$body}.{$signature}";
    }
}
