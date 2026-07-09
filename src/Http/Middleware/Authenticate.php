<?php

declare(strict_types=1);

namespace Tavp\Core\Http\Middleware;

use Tavp\Core\Http\Request;
use Tavp\Core\Http\Response;

/**
 * Checks that the user is authenticated via JWT token.
 * Redirects to login page for web requests, returns 401 for API requests.
 */
class Authenticate implements Middleware
{
    public function handle(callable $next): mixed
    {
        $request = new Request();
        $token = $this->extractToken($request);

        if ($token === null) {
            return $this->unauthorized();
        }

        /** @var \Tavp\Core\Auth\TokenService $tokenService */
        $tokenService = app('tokens');
        $payload = $tokenService->decode($token);

        if ($payload === null || ($payload['type'] ?? '') !== 'access') {
            return $this->unauthorized();
        }

        // Store user ID in request for downstream use
        $_SERVER['AUTH_USER_ID'] = (int) $payload['sub'];

        return $next();
    }

    private function extractToken(Request $request): ?string
    {
        // Check Authorization header: Bearer <token>
        $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
        if (preg_match('/^Bearer\s+(.+)$/i', $authHeader, $matches)) {
            return $matches[1];
        }

        // Check cookie
        $cookieToken = $_COOKIE['tavp_token'] ?? null;
        if ($cookieToken !== null) {
            return $cookieToken;
        }

        // Check session
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        return $_SESSION['auth_token'] ?? null;
    }

    private function unauthorized(): Response
    {
        $accept = $_SERVER['HTTP_ACCEPT'] ?? '';

        if (str_contains($accept, 'application/json')) {
            return (new Response())->json([
                'success' => false,
                'error' => [
                    'code' => 401,
                    'message' => 'Unauthenticated.',
                ],
            ], 401);
        }

        return (new Response())
            ->header('Location', '/login')
            ->setStatusCode(302);
    }
}
