<?php

declare(strict_types=1);

namespace Tavp\Kit;

/**
 * API token management for mobile/external clients.
 */
class ApiTokenService
{
    /**
     * Generate a new API token for a user.
     */
    public function generateToken(int $userId, string $deviceName = ''): string
    {
        $token = bin2hex(random_bytes(32));
        $hash = hash('sha256', $token);

        // Store hash in database (not the token itself)
        $this->storeToken($userId, $hash, $deviceName);

        return $token;
    }

    /**
     * Verify an API token.
     */
    public function verifyToken(string $token): ?array
    {
        $hash = hash('sha256', $token);
        return $this->findToken($hash);
    }

    /**
     * Revoke an API token.
     */
    public function revokeToken(string $token): bool
    {
        $hash = hash('sha256', $token);
        return $this->deleteToken($hash);
    }

    /**
     * Revoke all tokens for a user.
     */
    public function revokeAllTokens(int $userId): bool
    {
        // Implementation: delete all tokens for user
        return true;
    }

    private function storeToken(int $userId, string $hash, string $deviceName): void
    {
        // Implementation: save to api_tokens table
    }

    private function findToken(string $hash): ?array
    {
        // Implementation: find in api_tokens table
        return null;
    }

    private function deleteToken(string $hash): bool
    {
        // Implementation: delete from api_tokens table
        return true;
    }
}
