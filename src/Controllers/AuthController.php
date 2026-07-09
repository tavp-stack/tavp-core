<?php

declare(strict_types=1);

namespace Tavp\Core\Controllers;

use Tavp\Core\Auth\AuthService;
use Tavp\Core\Auth\OtpService;
use Tavp\Core\Auth\TokenService;

/**
 * Web authentication controller — OTP-first login flow.
 *
 * Routes:
 *   GET  /login          → show identifier form
 *   POST /login          → send OTP
 *   GET  /verify-otp     → show OTP form
 *   POST /verify-otp     → verify OTP, start session
 *   POST /logout         → end session
 */
class AuthController extends BaseController
{
    public function login(): string
    {
        return $this->view('auth.login');
    }

    public function sendOtp(): string
    {
        $identifier = $this->request->input('identifier', '');
        $channel = $this->request->input('channel', 'email');

        /** @var OtpService $otp */
        $otp = app('otp');
        $otp->createOtp($identifier, $channel);

        // In production the OTP is delivered via email/SMS/WhatsApp.
        // Here we return it in the view for local development only.
        return $this->view('auth.verify', [
            'identifier' => $identifier,
            'dev_code' => 'check logs',
        ]);
    }

    public function verifyOtp(): string
    {
        $identifier = $this->request->input('identifier', '');
        $code = $this->request->input('code', '');

        /** @var AuthService $auth */
        $auth = app('auth');
        $tokens = $auth->verifyOtpAndLogin($identifier, $code);

        if ($tokens === null) {
            return $this->view('auth.verify', ['error' => 'Invalid or expired code.']);
        }

        session('access_token', $tokens['access_token']);

        return $this->redirect('/dashboard');
    }

    public function logout(): string
    {
        session_destroy();

        return $this->redirect('/login');
    }
}
