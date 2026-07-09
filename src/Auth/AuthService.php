<?php

declare(strict_types=1);

namespace Tavp\Core\Auth;

use Tavp\Core\Database\Models\User;

/**
 * Orchestrates the OTP-first authentication flow.
 *
 * Per decision 10.11: login = request OTP → verify OTP → issue tokens.
 * Passwords are intentionally NOT part of the primary flow.
 */
class AuthService
{
    public function __construct(
        private OtpService $otpService,
        private TokenService $tokenService,
    ) {
    }

    /**
     * Step 1: send an OTP to the user's identifier.
     */
    public function sendOtp(string $identifier, string $channel = 'email'): string
    {
        return $this->otpService->createOtp($identifier, $channel);
    }

    /**
     * Step 2: verify the OTP and, if valid, return a token pair.
     * Creates the user automatically on first login (passwordless).
     */
    public function verifyOtpAndLogin(string $identifier, string $code): ?array
    {
        if (!$this->otpService->verifyOtp($identifier, $code)) {
            return null;
        }

        $user = User::query()->where('email', $identifier)->first()
            ?? User::query()->where('phone', $identifier)->first();

        if ($user === null) {
            $user = User::create([
                'email' => $identifier,
                'name' => $identifier,
            ]);
        }

        return $this->tokenService->createTokenPair((int) $user->id);
    }

    /**
     * Return the user for a valid access token, or null.
     */
    public function currentUser(string $accessToken): ?User
    {
        $payload = $this->tokenService->decode($accessToken);
        if ($payload === null || ($payload['type'] ?? '') !== 'access') {
            return null;
        }

        return User::findById($payload['sub']);
    }
}
