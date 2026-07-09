<?php

declare(strict_types=1);

namespace Tavp\Core\Controllers\Api;

use Tavp\Core\Auth\AuthService;
use Tavp\Core\Controllers\ApiController;
use Tavp\Core\Http\Request;

/**
 * API authentication — OTP-first, returns JSON.
 *
 * POST /api/v1/auth/send-otp   → request an OTP
 * POST /api/v1/auth/verify-otp → verify and receive tokens
 * POST /api/v1/auth/refresh    → exchange refresh token
 */
class AuthApiController extends ApiController
{
    public function sendOtp(): \Tavp\Core\Http\Response
    {
        $request = new Request();
        $identifier = $request->input('identifier', '');
        $channel = $request->input('channel', 'email');

        if ($identifier === '') {
            return $this->error('Identifier is required.', 422);
        }

        /** @var AuthService $auth */
        $auth = app('auth');
        $auth->sendOtp($identifier, $channel);

        return $this->success(['sent' => true]);
    }

    public function verifyOtp(): \Tavp\Core\Http\Response
    {
        $request = new Request();
        $identifier = $request->input('identifier', '');
        $code = $request->input('code', '');

        /** @var AuthService $auth */
        $auth = app('auth');
        $tokens = $auth->verifyOtpAndLogin($identifier, $code);

        if ($tokens === null) {
            return $this->error('Invalid or expired OTP.', 401);
        }

        return $this->success($tokens);
    }

    public function refresh(): \Tavp\Core\Http\Response
    {
        $request = new Request();
        $refreshToken = $request->input('refresh_token', '');

        if ($refreshToken === '') {
            return $this->error('Refresh token is required.', 422);
        }

        /** @var \Tavp\Core\Auth\TokenService $tokenService */
        $tokenService = app('tokens');
        $payload = $tokenService->decode($refreshToken);

        if ($payload === null || ($payload['type'] ?? '') !== 'refresh') {
            return $this->error('Invalid refresh token.', 401);
        }

        $userId = (int) $payload['sub'];
        $tokens = $tokenService->createTokenPair($userId);

        return $this->success($tokens);
    }
}
