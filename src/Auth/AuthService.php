<?php

declare(strict_types=1);

namespace Tavp\Core\Auth;

use Tavp\Core\Database\Models\User;

/**
 * Orchestrates the OTP-first authentication flow.
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
     */
    public function verifyOtpAndLogin(string $identifier, string $code): ?array
    {
        if (!$this->otpService->verifyOtp($identifier, $code)) {
            return null;
        }

        $user = User::findFirst([
            'conditions' => 'email = :email: OR phone = :phone:',
            'bind' => ['email' => $identifier, 'phone' => $identifier],
        ]);

        if ($user === null) {
            $user = new User();
            $user->assign([
                'email' => $identifier,
                'name' => $identifier,
            ]);
            $user->save();
        }

        return $this->tokenService->createTokenPair($user->id);
    }

    public function currentUser(string $accessToken): ?object
    {
        $payload = $this->tokenService->decode($accessToken);
        if ($payload === null || ($payload['type'] ?? '') !== 'access') {
            return null;
        }

        return User::findFirst($payload['sub']);
    }
}
