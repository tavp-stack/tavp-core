<?php

declare(strict_types=1);

namespace Tavp\Core\Auth;

/**
 * Magic Link authentication — passwordless login via email link.
 */
class MagicLinkService
{
    public function __construct(
        private TokenService $tokenService,
        private MailService $mail
    ) {
    }

    /**
     * Generate a magic link token and send it via email.
     */
    public function sendMagicLink(string $email): bool
    {
        $token = bin2hex(random_bytes(32));
        $expires = time() + (15 * 60); // 15 minutes

        // Store token (in production, save to database)
        // For now, we use JWT with magic link claim

        $jwtToken = $this->tokenService->encode([
            'email' => $email,
            'type' => 'magic_link',
            'exp' => $expires,
        ]);

        $magicUrl = env('APP_URL', 'http://localhost') . "/auth/magic-link/{$jwtToken}";

        $subject = 'Your TAVP Login Link';
        $body = "Click the link below to log in:\n\n{$magicUrl}\n\nThis link expires in 15 minutes.";

        return $this->mail->send($email, $subject, $body);
    }

    /**
     * Verify a magic link token and return the user email.
     */
    public function verify(string $token): ?string
    {
        try {
            $payload = $this->tokenService->decode($token);

            if (!isset($payload['type']) || $payload['type'] !== 'magic_link') {
                return null;
            }

            if (!isset($payload['exp']) || $payload['exp'] < time()) {
                return null;
            }

            return $payload['email'] ?? null;
        } catch (\Exception $e) {
            return null;
        }
    }
}
