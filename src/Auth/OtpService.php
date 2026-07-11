<?php

declare(strict_types=1);

namespace Tavp\Core\Auth;

use Tavp\Core\Database\Models\OtpCode;

/**
 * Generates, stores and verifies one-time passwords (OTP).
 */
class OtpService
{
    public function __construct(
        private int $ttlMinutes = 5,
        private int $maxAttempts = 5,
    ) {
    }

    /**
     * Create and persist a new OTP.
     */
    public function createOtp(string $identifier, string $channel = 'email'): string
    {
        $code = (string) random_int(100000, 999999);
        $hash = hash('sha256', $code);

        $otpCode = new OtpCode();
        $otpCode->assign([
            'identifier' => $identifier,
            'code_hash' => $hash,
            'channel' => $channel,
            'expires_at' => date('Y-m-d H:i:s', strtotime("+{$this->ttlMinutes} minutes")),
            'attempts' => 0,
        ]);
        $otpCode->save();

        return $code;
    }

    /**
     * Hash an OTP code for storage/comparison.
     */
    public function hash(string $code): string
    {
        return hash('sha256', $code);
    }

    /**
     * Verify a submitted OTP.
     */
    public function verifyOtp(string $identifier, string $code): bool
    {
        $record = OtpCode::findFirst([
            'conditions' => 'identifier = :identifier:',
            'bind' => ['identifier' => $identifier],
            'order' => 'id DESC',
        ]);

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

        $record->delete();
        return true;
    }
}
