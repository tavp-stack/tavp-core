<?php

declare(strict_types=1);

namespace Tavp\Core\Auth;

use Tavp\Core\Database\Models\OtpCode;

/**
 * Generates, stores and verifies one-time passwords (OTP).
 *
 * Per decision 10.11, OTP is the PRIMARY login method — not passwords.
 * Codes are 6 digits, hashed with SHA-256, valid for 5 minutes and
 * limited to 5 verification attempts.
 */
class OtpService
{
    public function __construct(
        private int $ttlMinutes = 5,
        private int $maxAttempts = 5,
    ) {
    }

    /**
     * Create and persist a new OTP for the given identifier (email/phone).
     * Returns the plain code so it can be delivered (email/SMS/WhatsApp).
     */
    public function createOtp(string $identifier, string $channel = 'email'): string
    {
        $code = (string) random_int(100000, 999999);
        $hash = hash('sha256', $code);

        OtpCode::create([
            'identifier' => $identifier,
            'code_hash' => $hash,
            'channel' => $channel,
            'expires_at' => date('Y-m-d H:i:s', strtotime("+{$this->ttlMinutes} minutes")),
            'attempts' => 0,
        ]);

        return $code;
    }

    /**
     * Verify a submitted OTP. Returns true on success.
     * Increments the attempt counter and rejects after maxAttempts.
     */
    public function verifyOtp(string $identifier, string $code): bool
    {
        $record = OtpCode::query()
            ->where('identifier', $identifier)
            ->orderBy('id', 'desc')
            ->first();

        if ($record === null) {
            return false;
        }

        if ((int) $record->attempts >= $this->maxAttempts) {
            return false;
        }

        if (strtotime((string) $record->expires_at) < time()) {
            return false;
        }

        $expected = hash('sha256', $code);
        $matches = hash_equals((string) $record->code_hash, $expected);

        if (!$matches) {
            $record->attempts = (int) $record->attempts + 1;
            $record->save();

            return false;
        }

        // Consume the OTP so it cannot be reused.
        $record->delete();

        return true;
    }
}
