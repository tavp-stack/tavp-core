<?php

declare(strict_types=1);

namespace Tavp\Core\Auth\Totp;

/**
 * Time-based one-time password (TOTP) for second-factor auth.
 *
 * Generates a provisioning URI (with QR) and verifies a submitted code.
 * Uses a standard HMAC-SHA1 time step, compatible with Google
 * Authenticator / Authy.
 */
class TotpService
{
    private int $digits = 6;
    private int $stepSeconds = 30;

    public function __construct(private string $secret)
    {
    }

    /**
     * Generate the current TOTP code for the given timestamp.
     */
    public function generate(int $time = null): string
    {
        $time ??= time();
        $counter = intdiv($time, $this->stepSeconds);
        $hash = hash_hmac('sha1', pack('J', $counter), $this->secret, true);
        $offset = ord($hash[19]) & 0xf;
        $code = (unpack('N', substr($hash, $offset, 4))[1] & 0x7fffffff) % (10 ** $this->digits);

        return str_pad((string) $code, $this->digits, '0', STR_PAD_LEFT);
    }

    /**
     * Verify a submitted code within a small window (allows clock drift).
     */
    public function verify(string $code, int $window = 1): bool
    {
        $now = time();
        for ($i = -$window; $i <= $window; $i++) {
            if (hash_equals($this->generate($now + $i * $this->stepSeconds), $code)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Build an otpauth:// URI for QR code generation.
     */
    public function provisioningUri(string $accountName): string
    {
        return sprintf(
            'otpauth://totp/%s?secret=%s&issuer=TAVP&digits=%d&period=%d',
            urlencode($accountName),
            $this->secret,
            $this->digits,
            $this->stepSeconds
        );
    }
}
